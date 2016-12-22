<?php
namespace Application\CrmBundle\Command;

use Application\UserBundle\Entity\User;
use Octet\Ticketing\Bundle\Entity\Reminder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReminderAssigneeToOidCommand extends AbstractTicketingMigrateCommand
{
    protected function configure()
    {
        $this->setName('app:migrate:reminder:assignee');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dropTable(static::REMINDER_TEMP_TABLE);

        $reminders = $this->getReminderRepository()->findAll();

        foreach ($reminders as $reminder) {
            /** @var Reminder $reminder */

            /** @var User $user */
            $user = $this->getTargetEntity($reminder->getAssignee());
            $this->insertToTmpTable(static::REMINDER_TEMP_TABLE, $reminder->getId(), $user->getObjectIdentity()->getId());
        }

    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|\Octet\Ticketing\Bundle\Entity\Repository\ReminderRepository
     */
    protected function getReminderRepository() {
        return $this->getDoctrine()->getRepository('OctetTicketingBundle:Reminder');
    }


}