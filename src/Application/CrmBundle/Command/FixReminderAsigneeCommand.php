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
        $reminders = $this->getDoctrine()->getRepository('OctetTicketingBundle:Reminder')->findAll();

        foreach ($reminders as $reminder) {

            $assigneeOids = $this->getPdo()->query(sprintf('SELECT * FROM %s WHERE subject = %d', static::REMINDER_TEMP_TABLE, $reminder->getId()));

            foreach($assigneeOids as $row) {
                $objectIdentity = $this->getDoctrine()->getRepository('ApplicationObjectIdentityBundle:ObjectIdentity')->find($row['oid']);
                $reminder->setAssignee($objectIdentity->getReference());
            }

            $this->getDoctrine()->getManager()->flush($reminder);
        }
    }


}