<?php
namespace Application\CrmBundle\Command;

use Octet\Ticketing\Bundle\Entity\Note;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixNoteRelatedObjectsCommand extends AbstractTicketingMigrateCommand
{
    protected function configure()
    {
        $this->setName('app:fix:note:related');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Note[] $notes */
        $notes = $this->getDoctrine()->getRepository('OctetTicketingBundle:Note')->findAll();

        foreach ($notes as $note) {
            $author_id = $this->getPdo()->query(sprintf('SELECT author FROM %s WHERE 0id = %d', static::NOTE_TEMP_TABLE, $note->getId()))->fetch(\Pdo::FETCH_COLUMN);
            $createdAt = $this->getPdo()->query(sprintf('SELECT created_at FROM %s WHERE id = %d', static::NOTE_TEMP_TABLE, $note->getId()))->fetch(\Pdo::FETCH_COLUMN);

            $user = $this->getDoctrine()->getRepository('ApplicationUserBundle:User')->find($author_id);

            $note->setAuthor($user);

            $relatedObjectIds = $this->getPdo()->query(sprintf('SELECT * FROM %s WHERE subject = %d', static::NOTE_OID_TEMP_TABLE, $note->getId()));

            foreach($relatedObjectIds as $row) {
                $objectIdentity = $this->getDoctrine()->getRepository('ApplicationObjectIdentityBundle:ObjectIdentity')->find($row['oid']);
                $note->addRelatedObjectIdentity($objectIdentity);
            }

            $note->setCreatedAt(new \DateTime($createdAt));

            $this->getDoctrine()->getManager()->flush($note);
        }
    }


}