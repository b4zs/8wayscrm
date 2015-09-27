<?php

namespace Core\LoggableEntityBundle\EventListener;

use Core\LoggableEntityBundle\Entity\LogEntry;
use Core\LoggableEntityBundle\Model\LogExtraData;
use Core\LoggableEntityBundle\Model\LogExtraDataAware;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoggableListener extends \Gedmo\Loggable\LoggableListener
{
	/** @var  TokenStorageInterface */
	private $tokenStorage;

	protected function prePersistLogEntry($logEntry, $object)
	{
		/** @var LogEntry $logEntry */

		if ($object instanceof LogExtraDataAware && null !== $object->getLogExtraData()) {
			$logEntry->setComment($object->getLogExtraData()->comment);
			$logEntry->setCustomAction($object->getLogExtraData()->customAction);
			$logEntry->setExtraData($object->getLogExtraData()->extraData);
		}

		if (array_key_exists('_extra_data', $logEntry->getData())) {
			$logEntry->setData(array_diff_assoc($logEntry->getData(), array('_extra_data' => true,)));
		}
	}

	public function onKernelController()
	{
		if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->isAuthenticated()) {
			$userName = $this->tokenStorage->getToken()->getUsername();
			$this->setUsername($userName);
		}
	}

	public function setTokenStorage(TokenStorageInterface $tokenStorage)
	{
		$this->tokenStorage = $tokenStorage;
	}

	public function getLogEntryClassnameForClass(EntityManager $entityManager, $classname)
	{
		return $this->getLogEntryClass($this->getEventAdapter(new OnFlushEventArgs($entityManager)), $classname);
	}

	/**
	 * Returns an objects changeset data
	 *
	 * @param LoggableAdapter $ea
	 * @param object $object
	 * @param object $logEntry
	 *
	 * @return array
	 */
	protected function getObjectChangeSetData($ea, $object, $logEntry)
	{
		$newValues = parent::getObjectChangeSetData($ea, $object, $logEntry);

		if ($object instanceof LogExtraDataAware
			&& $object->getLogExtraData() instanceof LogExtraData
			&& $object->getLogExtraData()->hasData()) {
			$newValues['_extra_data'] = true;
		}

		return $newValues;
	}


}