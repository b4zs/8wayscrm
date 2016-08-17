<?php


namespace Application\CrmBundle\Command;

use Application\CrmBundle\Entity\Address;
use Application\CrmBundle\Entity\Client;
use Application\CrmBundle\Entity\Contact;
use Application\CrmBundle\Entity\SectorOfActivity;
use Application\CrmBundle\Enum\ClientStatus;
use Application\UserBundle\Entity\User;
use Core\LoggableEntityBundle\Entity\LogEntry;
use Core\ToolsBundle\Enum\Gender;
use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnitOfWork;
use Exporter\Source\CsvSourceIterator;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Yaml\Yaml;

class ImportClientsCommand extends ContainerAwareCommand
{
	private $existingSectors = null;

	protected $updatedClients = 0;

	protected $newClients = 0;

	protected $unprocessedClients = 0;

	protected function configure()
	{
		$this->setName('crm:import:clients');
		$this->addArgument('filename');
		$this->addOption('from', null, InputOption::VALUE_OPTIONAL);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->removeSectors();
		foreach (\Application\CrmBundle\Enum\SectorOfActivity::getChoices() as $choice) {
			$this->ensureSector($choice);
		}

		$from = $input->getOption('from');

		$excelObject = \PHPExcel_IOFactory::load($input->getArgument('filename'));
		$sheet = $excelObject->getSheet(0);

		$progressBar = new ProgressBar($output, $sheet->getHighestRow());
		$keys = null;
		foreach ($sheet->getRowIterator() as $rowIndex => $excelRow) {
			if (null === $keys) {
				$keys = array();
				/** @var \PHPExcel_Cell $excelCell */
				foreach ($excelRow->getCellIterator() as $excelCell) {
					$keys[] = $this->normalizeKey($excelCell->getValue());
				}
			} else {
				if ($from && $from > $rowIndex) continue;
				$progressBar->setProgress($rowIndex);
				$progressBar->setMessage('Processing '.$rowIndex.'/'.$sheet->getHighestRow());

				$values = array();
				/** @var \PHPExcel_Cell $excelCell */
				foreach ($excelRow->getCellIterator() as $excelCell) {
					$values[] = $excelCell->getCalculatedValue();
				}
				$this->processCsvRow(array_combine($keys, $values));
			}
		}

		$output->writeln(PHP_EOL . sprintf('done. updated: %d, new: %d, skipped: %d', $this->updatedClients, $this->newClients, $this->unprocessedClients));
	}

	private function normalizeKey($key)
	{
		$key = \Doctrine\Common\Util\Inflector::camelize($key);
		$key = str_replace('?', '', $key);

		return $key;
	}

	private function fetchUser($sales)
	{
		static $map = array(
			'JK' => 'julienkraus',
			'APB' => 'arnaudplenet',
			'JJ' => 'admin',
			'PM' => 'admin',
			'EC' => 'admin',
			'GP' => 'admin',
		);

		$repository = $this->getEntityManager()->getRepository('ApplicationUserBundle:User');
		if (isset($map[$sales])) {
			$user = $repository->findOneBy(array('usernameCanonical' => $map[$sales]));
			if (null === $user) {
				throw new \RuntimeException('User not found by usernameCanonical: ' .json_encode($map[$sales]));
			} else {
				return $user;
			}
		} else {
			throw new \InvalidArgumentException('Sales "'.$sales.'" not found in map');
		}
	}

	private function processCsvRow(array $row)
	{
		$name = trim($row['company']);
		if (!$name) {
			if (preg_match('/http\:\/\/(www\.)?([a-zA-Z0-9\-]+)\.(com|ch)/', $row['website'], $out)) {
				$name = ucfirst($out[2]);
			} else {
				$name = $row['website'];//whatever...
			}
		}

		if(null === $name) {
			$this->unprocessedClients++;
			return; //???
		}


		//name
		$client = $this->findClientByName($name);

		if($client instanceof Client && null !== $client->getDeletedAt()) {
			$this->unprocessedClients++;
			return;
		}

		if (!$client) {
			$client = new Client();
			$client->getCompany()->setName($name);
			$client->getFileset()->setName($name);
			$this->newClients++;
		} else {
			$this->updatedClients++;
		}

		//group
		$client->addGroup($this->getTargetGroup());

		//website
        if (!empty($row['website'])) {
		    $client->getCompany()->setWebsite($row['website']);
        }

		//sales
        if (!empty($row['sales'])) {
		    $client->setOwner($this->fetchUser($row['sales']));
        }

		//activity
		if ($row['activity']) {
			$client->getCompany()->setSectorOfActivity($row['activity']);
			$this->ensureSector($client->getCompany()->getSectorOfActivity());
		}


		//person of contact
		$contact = null;
		$contactName = $row['personOfContact'];
		foreach ($client->getContacts() as $existingContact) {
			if ($contactName == $existingContact->getPerson()->getFullName()) {
				$contact = $existingContact;
			}
		}

		if (null === $contact) {
			$contact = new Contact();
			$contact->getPerson()->setFullName($contactName);
			$client->addContact($contact);
		}

		//phone number
        if (!empty($row['phoneNumber'])) {
		    $contact->getPerson()->setCompanyPhone($row['phoneNumber']);
        }

		//gender
		switch (trim(strtolower(strtr($row['gender'], array('.'=>'',':'=>'',))))) {
			case 'mrs':
			case 'ms':
			case 'mlle':
			case 'mme':
				$contact->getPerson()->setGender(Gender::FEMALE);
				break;
			case 'mr':
			case 'm':
			case 'mtre':
			case 'dr':
				$contact->getPerson()->setGender(Gender::MALE);
				break;
			case 'n/a':
			case '':
				break;
			default:
				throw new \RuntimeException('Gender value not handled:'.$row['gender']);
		}

		//email
        if (!empty($row['eMail'])) {
		    $contact->getPerson()->setCompanyEmail($row['eMail']);
        }

		//address
		$address = $client->getAddresses()->first();
		if (!$address instanceof Address) {
			$address = new Address();
			$client->addAddress($address);
		}
        if (!empty($row['address'])) {
		    $address->setStreet($row['address']);
        }

		//last contact
		if (is_numeric($row['lastContact'])) {
			$row['lastContact'] = date('Y-m-d', strtotime('1900-Jan-0') + $row['lastContact'] * 3600 * 24);
		} elseif($row['lastContact'] == '') {
			$row['lastContact'] = (new \DateTime())->format('Y-m-d');
		}else{
			$row['lastContact'] = strtr($row['lastContact'], array(
				'fev.' => 'feb ',
				'04-30-15' => '30-4-15',
				'04-15-15' => '15-4-15',
			));
		}

        $lastContactTimestamp = strtotime($row['lastContact']);

        if (!$client->getCreatedAt()) {
		    $client->setCreatedAt((new \DateTime())->setTimestamp($lastContactTimestamp));
        }

        if (!$client->getUpdatedAt() || $client->getUpdatedAt()->getTimestamp() < $lastContactTimestamp) {
		    $client->setUpdatedAt((new \DateTime())->setTimestamp($lastContactTimestamp));
        }

		//action
		/** @var LogEntryRepository $logRepository */
		$logRepository = $this->getEntityManager()->getRepository('CoreLoggableEntityBundle:LogEntry');
		$logEntries = $logRepository->getLogEntries($client);
        $versions = array();
		foreach ($logEntries as $logEntry) {
            $versions[] = (int)$logEntry->getVersion();
		}
		$version = count($versions) ? max($versions)+1 : 0;

		//interest + meeting scheduled + offer sent
		$row['meetingScheduled'] = \Doctrine\Common\Util\Inflector::camelize($row['meetingScheduled']);
		if ($row['meetingScheduled'] === 'n/a') {
			$row['meetingScheduled'] = null;
		}

        if (!isset($row['comment'])) {
            $row['comment'] = '';
        }

        list($status, $state, $version) = $this->handleClientState($row, $client, $version);

		if($this->getEntityManager()->getUnitOfWork()->getEntityState($client) === UnitOfWork::STATE_MANAGED) {
			if ($status != $client->getStatus()) {
				$row['comment'] .= PHP_EOL.'[system] updating status during reimport: '.sprintf('"%s" to "%s"', $client->getStatus(), $status);
				$client->setStatus($status);
			}
		} else {
			$row['comment'] .= PHP_EOL.'[system] inserted new client during reimport';
			$client->setStatus($status);
			$this->getEntityManager()->persist($client);
		}

		$this->getEntityManager()->flush($client);

		$this->createEntityLog($client, 'update',  'import', $row['comment'], $version);
	}

	private function findClientByName($company)
	{
		$this->getEntityManager()->getFilters()->disable('softdeleteable');

		$ret = $this->getEntityManager()->getRepository('ApplicationCrmBundle:Client')->findOneBy(array(
			'company.name' => $company,
		));

		$this->getEntityManager()->getFilters()->enable('softdeleteable');

		return $ret;
	}


	private function getEntityManager()
	{
		return $this->getContainer()->get('doctrine.orm.default_entity_manager');
	}

	private function createEntityLog(Client $client, $action, $customAction, $comment, &$version)
	{
		$actionLog = new LogEntry();
		$actionLog->setUsername('import');
		$actionLog->setObjectClass(get_class($client));
		$actionLog->setObjectId($client->getId());
		$actionLog->setLoggedAt(new \DateTime());
		$actionLog->setVersion($version++);
		$actionLog->setAction($action);
		$actionLog->setCustomAction($customAction);
		$actionLog->setComment($comment);
		$this->getEntityManager()->persist($actionLog);
		$this->getEntityManager()->flush($actionLog);
	}

	private function ensureSector($name)
	{
		if (null === $this->existingSectors) {
			$this->existingSectors = array();
			$sectors = $this->getEntityManager()->getRepository('ApplicationCrmBundle:SectorOfActivity')->findAll();
			/** @var SectorOfActivity $sector */
			foreach ($sectors as $sector) {
				$this->existingSectors[$sector->getName()] = $sector;
			}
		}

		if (!array_key_exists($name, $this->existingSectors)) {
			$sector = new SectorOfActivity();
			$sector->setName($name);
			$this->existingSectors[$name] = $sector;

			$this->getEntityManager()->persist($sector);
			$this->getEntityManager()->flush($sector);
		}


	}

	private function removeSectors()
	{
		/** @var EntityRepository $sectorsRepository */
		$sectorsRepository = $this->getEntityManager()->getRepository('ApplicationCrmBundle:SectorOfActivity');

		$sectorsRepository->clear();
	}

	private function getTargetGroup()
	{
		return $this->getEntityManager()->getRepository('ApplicationUserBundle:Group')->findOneBy(array(
			'name' => '8ways',
		));
	}

    private function handleClientState(array $row, $client, $version)
    {
        $state = array();
        $state['sleeping'] = null;
        $state['signed'] = null;
        $state['offerSent'] = null;
        $state['called'] = trim(strtolower($row['action'])) === 'called';
        $state['interested'] = null;
        $state['meetingScheduled'] = null;
        $state['lastAction'] = trim(strtolower($row['action']));

        switch (strtolower($row['offerSent'])) {
            case '':
            case 'no':
                $state['offerSent'] = false;
                break;
            case 'yes':
                $state['offerSent'] = true;
                break;
            case 'signed':
                $state['offerSent'] = true;
                $state['signed'] = true;
                break;
            default:
                throw new \RuntimeException('offerSent value not handled:' . json_encode($row['offerSent']));
        }

        switch (trim(strtolower($row['interest']))) {
            case 'no interest':
            case 'not interested':
            case 'no':
            case 'not interesting':
                $state['interested'] = false;
                break;
            case 'not at the moment':
            case 'yes but not now':
            case 'not now':
                $state['interested'] = false;
                $state['sleeping'] = true;
				break;
            case 'interested':
            case 'positive':
            case 'yes':
            case 'yes, probably':
            case 'probably':
            case 'maybe':
            case 'yes, but':
                $state['interested'] = true;
                break;
            case 'n/a':
            case '':
            case 'unknown':
                break;
            default:
                throw new \RuntimeException('Interest value not handled:' . json_encode($row['interest']));
        }


        $row['meetingScheduled'] = strtr($row['meetingScheduled'], array(
            'à' => ' ',
        ));
        if (is_numeric($row['meetingScheduled'])) {
            $date = date('Y-m-d', strtotime('1900-Jan-0') + $row['meetingScheduled'] * 3600 * 24);
            $this->createEntityLog($client, 'update', 'meeting scheduled', "Meeting scheduled to " . $date, $version);
            $row['meetingScheduled'] = 'meeting';
        }
        switch (strtolower($row['meetingScheduled'])) {
            case 'email':
            case 'e-mail':
                $state['meetingScheduled'] = 'e-mail';
                break;
            case 'meeting':
            case 'maybe':
            case 'yes':
                $state['meetingScheduled'] = $row['meetingScheduled'];
                break;
            case '050115':
            case 'torecall10july':
            case 'torecalldecember':
            case '07.05.2015 11h30':
            case 'rappeler22mai':
            case 'torecall15.05':
            case 'torecallseptember2015':
            case 'waitforcallbackforapproval':
            case 'tocal07.05':
            case 'yes24.062015':
                $this->createEntityLog($client, 'update', 'meeting scheduled', "Meeting scheduled: " . $row['meetingScheduled'], $version);
                $state['meetingScheduled'] = $row['meetingScheduled'];
                break;
            case '':
            case null:
            case 'no':
                $state['meetingScheduled'] = false;
                break;
            case 'dead':
                $state['meetingScheduled'] = false;
                $state['interested'] = false;
                break;
            default:
                throw new \RuntimeException('meetingScheduled value not handled: ' . json_encode($row['meetingScheduled']));
        }

        if (!$state['lastAction']) {
            return array(ClientStatus::COLD, $state, $version);
        } elseif ($state['sleeping']) {
            return array(ClientStatus::SLEEPING, $state, $version);
        } elseif ($state['signed']) {
            return array(ClientStatus::ACTIVE, $state, $version);
        } elseif (!$state['offerSent'] && true === $state['interested'] && !$state['meetingScheduled']) {
            return array(ClientStatus::HOT, $state, $version);
        } elseif ($state['meetingScheduled']) {
            return array(ClientStatus::ACTIVE, $state, $version);
        } elseif ($state['offerSent'] && $state['interested']) {
            return array(ClientStatus::HOT, $state, $version);
        } elseif ($state['offerSent'] && !$state['interested']) {
            return array(ClientStatus::ARCHIVED, $state, $version);
        } elseif (!$state['offerSent'] && false === $state['interested'] && !$state['meetingScheduled']) {
            return array(ClientStatus::JUNK, $state, $version);
        } elseif (!$state['offerSent'] && null === $state['interested'] && !$state['meetingScheduled']) {
            return array(ClientStatus::COLD, $state, $version);
        } else {
            throw new \RuntimeException('Unhandled state: ' . PHP_EOL . json_encode($state));
        }
    }


}
