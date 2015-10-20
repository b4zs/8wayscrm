<?php

namespace Core\LoggableEntityBundle\EventListener;

use Core\LoggableEntityBundle\Entity\LogEntry;
use Core\LoggableEntityBundle\Model\LogExtraData;
use Core\LoggableEntityBundle\Model\LogExtraDataAware;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\PrePersist;
use Gedmo\Tool\Wrapper\AbstractWrapper;
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
		$om        = $ea->getObjectManager();
		$wrapped   = AbstractWrapper::wrap($object, $om);
		$meta      = $wrapped->getMetadata();
		$config    = $this->getConfiguration($om, $meta->name);
		$uow       = $om->getUnitOfWork();
		$newValues = array();

		foreach ($ea->getObjectChangeSet($uow, $object) as $field => $changes) {
			if (empty($config['versioned']) || !$this->isFieldVersioned($object, $field, $config, $changes)) {
				continue;
			}
			$value = $changes[1];
			if ($meta->isSingleValuedAssociation($field) && $value) {
				if ($wrapped->isEmbeddedAssociation($field)) {
					$value = $this->getObjectChangeSetData($ea, $value, $logEntry);
				} else {
					$oid          = spl_object_hash($value);
					$wrappedAssoc = AbstractWrapper::wrap($value, $om);
					$value        = $wrappedAssoc->getIdentifier(false);
					if (!is_array($value) && !$value) {
						$this->pendingRelatedObjects[$oid][] = array(
							'log'   => $logEntry,
							'field' => $field,
						);
					}
				}
			}
			$newValues[$field] = $value;
		}


		if ($object instanceof LogExtraDataAware
			&& $object->getLogExtraData() instanceof LogExtraData
			&& $object->getLogExtraData()->hasData()) {
			$newValues['_extra_data'] = true;
		}

		return $newValues;
	}

	private function isFieldVersioned($object, $field, $config, $changes)
	{
		return
			(count(array_filter($changes)) > 0)
			&& (
				in_array($field, $config['versioned'])
				|| (false !== strpos($field, '.'))  // this is a hack to let embedded (embeddable) changeset to be logged
			)
		;
	}


}