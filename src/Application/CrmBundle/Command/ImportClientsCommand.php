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
use Doctrine\ORM\EntityRepository;
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

		$output->writeln('done');
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

		//name
		$client = $this->findClientByName($name);
		if (!$client) {
			$client = new Client();
			$client->getCompany()->setName($name);
		}

		//group
		$client->addGroup($this->getTargetGroup());

		//website
		$client->getCompany()->setWebsite($row['website']);

		//sales
		$client->setOwner($this->fetchUser($row['sales']));

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
		$contact->getPerson()->setCompanyPhone($row['phoneNumber']);

		//gender
		switch (trim(strtolower(strtr($row['gender'], array('.'=>'',':'=>'',))))) {
			case 'mrs':
			case 'ms':
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
		$contact->getPerson()->setCompanyEmail($row['eMail']);

		//address
		$address = $client->getAddresses()->first();
		if (!$address instanceof Address) {
			$address = new Address();
			$client->addAddress($address);
		}
		$address->setStreet($row['address']);

		//last contact
		if (is_numeric($row['lastContact'])) {
			$row['lastContact'] = date('Y-m-d', strtotime('1900-Jan-0') + $row['lastContact'] * 3600 * 24);
		}else{
			$row['lastContact'] = strtr($row['lastContact'], array(
				'fev.' => 'feb ',
				'04-30-15' => '30-4-15',
				'04-15-15' => '15-4-15',
			));
		}
		$client->setCreatedAt(new \DateTime($row['lastContact']));
		$client->setUpdatedAt(new \DateTime($row['lastContact']));

		//action
		/** @var LogEntryRepository $logRepository */
		$logRepository = $this->getEntityManager()->getRepository('CoreLoggableEntityBundle:LogEntry');
		$logEntries = $logRepository->getLogEntries($client);
		foreach ($logEntries as $logEntry) {
			$this->getEntityManager()->remove($logEntry);
		}
		$version = 0;


		$this->getEntityManager()->persist($client);
		$this->getEntityManager()->flush($client);

		$this->createEntityLog($client, 'update',  $row['action'], $row['comment'], new \DateTime($row['lastContact']), $version);

		//interest + meeting scheduled + offer sent
		$row['meetingScheduled'] = \Doctrine\Common\Util\Inflector::camelize($row['meetingScheduled']);
		if ($row['meetingScheduled'] === 'n/a') {
			$row['meetingScheduled'] = null;
		}

		$state = array();
		$state['sleeping'] = null;
		$state['signed']  = null;
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
				throw new \RuntimeException('offerSent value not handled:'.json_encode($row['offerSent']));
		}

		switch (trim(strtolower($row['interest']))) {
			case 'no interest':
			case 'not interested':
			case 'no':
				$state['interested'] = false;
				break;
			case 'not at the moment':
			case 'yes but not now':
			case 'not now':
				$state['interested'] = false;
				$state['sleeping'] = true;
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
				throw new \RuntimeException('Interest value not handled:'.json_encode($row['interest']));
		}


		$row['meetingScheduled'] = strtr($row['meetingScheduled'], array(
			'à' => ' ',
		));
		if (is_numeric($row['meetingScheduled'])) {
			$date = date('Y-m-d', strtotime('1900-Jan-0') + $row['meetingScheduled'] * 3600 * 24);
			$this->createEntityLog($client, 'update',  'meeting scheduled', "Meeting scheduled to ".$date, new \DateTime($row['lastContact']), $version);
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
				$this->createEntityLog($client, 'update',  'meeting scheduled', "Meeting scheduled: ".$row['meetingScheduled'], new \DateTime($row['lastContact']), $version);
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
				throw new \RuntimeException('meetingScheduled value not handled: '.json_encode($row['meetingScheduled']));
		}

		if (!$state['lastAction']) {
			$client->setStatus(ClientStatus::COLD);
		} elseif ($state['sleeping']) {
			$client->setStatus(ClientStatus::SLEEPING);
		} elseif ($state['signed']) {
			$client->setStatus(ClientStatus::ACTIVE);
		} elseif (!$state['offerSent'] && true === $state['interested'] && !$state['meetingScheduled']) {
			$client->setStatus(ClientStatus::HOT);
		} elseif ($state['meetingScheduled']) {
			$client->setStatus(ClientStatus::ACTIVE);
		} elseif ($state['offerSent'] && $state['interested']) {
			$client->setStatus(ClientStatus::HOT);
		} elseif ($state['offerSent'] && !$state['interested']) {
			$client->setStatus(ClientStatus::ARCHIVED);
		} elseif (!$state['offerSent'] && false === $state['interested'] && !$state['meetingScheduled']) {
			$client->setStatus(ClientStatus::JUNK);
		} elseif (!$state['offerSent'] && null === $state['interested'] && !$state['meetingScheduled']) {
			$client->setStatus(ClientStatus::COLD);
		} else {
			throw new \RuntimeException('Unhandled state: '.PHP_EOL.json_encode($state));
		}

		$this->createEntityLog($client, 'update', 'status', Yaml::dump($state), new \DateTime('NOW'), $version);

		$this->getEntityManager()->flush($client);
	}

	private function findClientByName($company)
	{
		$repository = $this->getContainer()->get('doctrine')->getRepository('ApplicationCrmBundle:Client');
		return $repository->findOneBy(array(
			'company.name' => $company,
		));
	}


	private function getEntityManager()
	{
		return $this->getContainer()->get('doctrine.orm.default_entity_manager');
	}

	private function createEntityLog(Client $client, $action, $customAction, $comment, \DateTime $date, &$version)
	{
		$actionLog = new LogEntry();
		$actionLog->setUsername($client->getOwner()->getUsernameCanonical());
		$actionLog->setObjectClass(get_class($client));
		$actionLog->setObjectId($client->getId());
		$actionLog->setLoggedAt($date);
		$actionLog->setVersion(++$version);
		$actionLog->setAction($action);
		$actionLog->setCustomAction($customAction);
		$actionLog->setComment($comment);
		$this->getEntityManager()->persist($actionLog);
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


}
