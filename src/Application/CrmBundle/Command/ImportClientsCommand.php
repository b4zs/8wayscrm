<?php


namespace Application\CrmBundle\Command;

use Application\CrmBundle\Entity\Client;
use Exporter\Source\CsvSourceIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportClientsCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('crm:import:clients');
		$this->addArgument('filename');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$excelObject = \PHPExcel_IOFactory::load($input->getArgument('filename'));

		$sheet = $excelObject->getSheet(0);
		$keys = null;
		foreach ($sheet->getRowIterator() as $excelRow) {
			if (null === $keys) {
				$keys = array();
				/** @var \PHPExcel_Cell $excelCell */
				foreach ($excelRow->getCellIterator() as $excelCell) {
					$keys[] = $this->normalizeKey($excelCell->getValue());
				}
			} else {
				$values = array();
				/** @var \PHPExcel_Cell $excelCell */
				foreach ($excelRow->getCellIterator() as $excelCell) {
					$values[] = $excelCell->getValue();
					if ($excelCell->getC
				}
				$row = array_combine($keys, $values);
				print_r($row);die;
			}
		}
	}

	private function normalizeKey($key)
	{
		$key = \Doctrine\Common\Util\Inflector::camelize($key);
		$key = str_replace('?', '', $key);

		return $key;
	}

	private function processCsvRow(array $row)
	{

		$client = new Client();
		$client->getCompany()->setName($row['company']);
		$client->getCompany()->setWebsite($row['website']);
		$client->setOwner($this->fetchUser($row['sales']));
	}

	private function fetchUser($sales)
	{
		static $map = array(
			'JJ' => 'Julienkraus'
		);

		$repository = $this->getContainer()->get('doctrine')->getRepository('ApplicationUserBundle:User');
		if (isset($map[$sales])) {
			$user = $repository->findOneBy(array('username' => $map[$sales]));
			if (null === $user) {
				throw new \RuntimeException('User not found by username:' .$map[$sales]);
			} else {
				return $user;
			}
		} else {
			throw new \InvalidArgumentException('Sales "'.$sales.'" not found in map');
		}
	}


}