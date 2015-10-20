<?php

namespace Core\LoggableEntityBundle\Controller;

use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class LoggableEntityHelperController extends Controller
{
    public function listLogsAction($className, $id)
    {
        return $this->render('CoreLoggableEntityBundle:LoggableEntityHelper:listLogs.html.twig', array(
            'logEntries' => $this
                ->container
                ->get('core.loggable_entity.admin.extension')
                ->buildLogEntriesQueryForEntity($className, $id)
                ->getResult(),
        ));
    }
}
