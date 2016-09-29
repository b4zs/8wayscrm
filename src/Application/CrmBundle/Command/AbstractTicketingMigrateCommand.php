<?php
namespace Application\CrmBundle\Command;


use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class AbstractTicketingMigrateCommand extends ContainerAwareCommand
{

    const NOTE_OID_TEMP_TABLE = 'temp__note_reletad_oid';
    const NOTE_TEMP_TABLE = 'temp__note';
    const REMINDER_TEMP_TABLE = 'temp__reminder';

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

    protected function dropTable($tableName) {
        $this->getPdo()->exec(sprintf('DROP TABLE IF EXISTS %s', $tableName));
    }

    protected function createTempTable($tableName) {
        $this->getPdo()->exec(sprintf("CREATE TABLE %s(`subject` INT NOT NULL , `oid` INT NOT NULL ) ENGINE = InnoDB;", $tableName));
    }

    protected function insertToTmpTable($tableName, $subject, $oid) {
        $this->getPdo()->exec(sprintf('INSERT INTO %s(`subject`, `oid`) VALUES(%d, %d)', $tableName, $subject, $oid));
    }

    protected function getNotes() {
        return $this->getPdo()->query('SELECT * FROM ticketing__note', \Pdo::FETCH_ASSOC);
    }

    /**
     * @return ObjectIdentityAware
     */
    protected function getTargetEntity($relatedObjectString) {
        $id = substr($relatedObjectString, 1 - (strlen($relatedObjectString) - strpos($relatedObjectString, '#')));
        $entityClass = str_replace('#' . $id, '', $relatedObjectString);

        $entity = $this->getDoctrine()->getRepository($entityClass)->find($id);

        return $entity;
    }

}