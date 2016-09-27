<?php
namespace Application\CrmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddressStreetFixCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:fix:address:street');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getPdo()->exec('UPDATE crm__address SET street = IF(crm__address.street_number IS NOT NULL, CONCAT(crm__address.street, " ", crm__address.street_number), crm__address.street)');
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|object
     */
    protected function getDoctrine() {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return \PDO
     */
    protected function getPdo() {
        return $this->getDoctrine()->getConnection();
    }


}