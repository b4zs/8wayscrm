<?php
namespace Application\CrmBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixReminderAsigneeCommand extends AbstractTicketingMigrateCommand
{
    protected function configure()
    {
        $this->setName('app:fix:reminder:asignee');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }


}