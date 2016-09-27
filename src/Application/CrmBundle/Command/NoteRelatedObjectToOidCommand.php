<?php
namespace Application\CrmBundle\Command;

use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NoteRelatedObjectToOidCommand extends AbstractTicketingMigrateCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('app:migrate:note:related');
        $this->setHelp('map note related object to oids');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dropTable(static::NOTE_OID_TEMP_TABLE);
        $this->dropTable(static::NOTE_TEMP_TABLE);
        $this->createTempTable(static::NOTE_OID_TEMP_TABLE);
        $this->createTempNoteTable();

        $notes = $this->getNotes();

        foreach ($notes as $note) {
            $author = $this->getTargetEntity($note['author']);
            $this->insertTempNote(
                $note['id'],
                $note['task_id'],
                $note['title'],
                $note['description'],
                $note['type'],
                $note['status'],
                $author->getId(),
                $note['created_at']
            );
        }

        $relatedObjects = $this->getNoteRelatedObjects();

        foreach ($relatedObjects as $key => $relatedArray) {
            foreach ($relatedArray as $object) {
                /** @var ObjectIdentityAware $object */
                $this->insertToTmpTable(static::NOTE_OID_TEMP_TABLE, $key, $object->getObjectIdentity()->getId());
            }
        }
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    protected function getNoteRelatedObjects() {
        $data   = $this->getPdo()->query('SELECT nnro.note_id, ro.object FROM note_note_related_object nnro JOIN ticketing__note_related_object ro ON nnro.note_related_object_id = ro.id', \Pdo::FETCH_ASSOC);
        $return = array();

        foreach($data as $row) {

            $entity = $this->getTargetEntity($row['object']);

            if(null === $entity) {
                continue;
            }

            if(!$entity instanceof ObjectIdentityAware) {
                throw new \ErrorException(sprintf('The entity[%s:%s] is not ObjectIdentityAware!', get_class($entity), $entity->getId()));
            }

            $return[$row['note_id']][] = $entity;
        }

        return $return;
    }

    protected function createTempNoteTable() {
        $this->getPdo()->exec(sprintf('CREATE TABLE `%s` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `task_id` int(11) DEFAULT NULL,
         `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
         `description` longtext COLLATE utf8_unicode_ci,
         `type` int(11) DEFAULT NULL,
         `status` int(11) NOT NULL,
         `author` INT(11) NOT NULL,
         `created_at` datetime NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=462 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci', static::NOTE_TEMP_TABLE));
    }

    protected function insertTempNote($id, $task_id, $title, $description, $type, $status, $author, $createdAt) {
        $stmt = $this->getPdo()->prepare(sprintf('INSERT INTO %s(`id`, `task_id`, `title`, `description`, `type`, `status`, `author`, `created_at`) VALUES(:id, :task_id, :title, :description, :type, :status, :author, :createdAt)', static::NOTE_TEMP_TABLE));
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':createdAt', $createdAt);

        $stmt->execute();
    }
}