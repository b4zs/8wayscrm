<?php

namespace Application\CrmBundle\Command;

use Application\MediaBundle\Entity\Gallery;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DbFixerCommand extends ContainerAwareCommand
{
	/**
	 * Configures the current command.
	 */
	protected function configure()
	{
		$this->setName('app:fix-db');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

		foreach ($em->getRepository('ApplicationCrmBundle:Project')->findAll() as $project) {
			if (!$project->getFileset()) {
				$project->setFileset($this->createGallery($project->getName()));
				$em->flush();
			}
		}
		$em->clear();

		foreach ($em->getRepository('ApplicationCrmBundle:Client')->findAll() as $client) {
			if (!$client->getFileset()) {
				$client->setFileset($this->createGallery($client->getCompany()->getName()));
				$em->flush();
			}
		}
		$em->clear();

	}

	private function createGallery($name)
	{
		$g = new Gallery();
		$g->setName($name);

		return $g;
	}


}