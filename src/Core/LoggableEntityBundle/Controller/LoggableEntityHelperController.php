<?php

namespace Core\LoggableEntityBundle\Controller;

use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class LoggableEntityHelperController extends Controller
{
    public function listLogsAction($className, $id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('ID parameter is not valid');
        }

        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $object = $entityManager->find($className, $id);

        $loggableEntryClassname = $this->container->get('gedmo.listener.loggable')->getLogEntryClassnameForClass($entityManager, $className);

        /** @var LogEntryRepository $repository */
        $repository = $entityManager->getRepository($loggableEntryClassname);
        if (!$repository instanceof LogEntryRepository) {
            throw new InvalidConfigurationException('Invalid loggable repository set for '.$loggableEntryClassname);
        }
        $logEntriesQuery = $repository
            ->createQueryBuilder('log')
            ->select('log')
            ->andWhere('log.objectClass = :class')
            ->andWhere('log.objectId = :id')
            ->setParameter('class', $className)
            ->setParameter('id', $id)
            ->orderBy('log.version', 'ASC')
            ->getQuery();

        $logEntries = $logEntriesQuery->getResult();

        return $this->render('CoreLoggableEntityBundle:LoggableEntityHelper:listLogs.html.twig', array(
            'logEntries' => $logEntries,
        ));
    }
}
