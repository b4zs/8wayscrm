<?php

namespace Core\LoggableEntityBundle\Admin\Extension;

use Application\CrmBundle\Entity\AbstractClient;
use Core\LoggableEntityBundle\Entity\LogEntry;
use Core\LoggableEntityBundle\Model\LogExtraData;
use Core\LoggableEntityBundle\Model\LogExtraDataAware;
use Doctrine\ORM\EntityManager;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Gedmo\Loggable\LoggableListener;
use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class LoggableEntityExtension extends AdminExtension
{
	/** @var  EntityManager */
	private $entityManager;

	/**
	 * @param EntityManager $entityManager
	 */
	public function setEntityManager($entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param LoggableListener $loggableListener
	 */
	public function setLoggableListener($loggableListener)
	{
		$this->loggableListener = $loggableListener;
	}

	/**
	 * @var LoggableListener
	 */
	private $loggableListener;

	public function configureFormFields(FormMapper $form)
	{
		if ('form' !== $form->getFormBuilder()->getType()->getName() //avoid execution in case of custom formtypes
			|| !$this->shouldUseExtension($form->getAdmin())) {
			return;
		}

		$form->getAdmin()->getSubject()->setLogExtraData(new LogExtraData());

		$form->with('Add note');

		//TODO: get these values for the specific object type
		$form->add('log_custom_action', 'choice', array(
			'required' => false,
			'choices'  => array(
				'phone call'    => 'Phone call',
				'email'         => 'Email',
				'meeting'       => 'Meeting',
			),
			'empty_value'   => 'Update',
			'expanded'      => true,
			'label'         => false,
			'property_path' => 'logExtraData.customAction',
			'attr'          => array('class' => 'list-inline'),
		));
		$form->add('log_extra_comment', 'textarea', array(
			'required'      => false,
			'label'         => false,
			'property_path' => 'logExtraData.comment',
		));

		$form->end();

		$form->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event, $eventName, EventDispatcher $eventDispatcher){
			$object = $event->getData();
			if ($object instanceof LogExtraDataAware && $object->getLogExtraData() instanceof LogExtraData) {
				if ($object->getLogExtraData()->hasData()) {
					$object->setUpdatedAt(new \DateTime());
				}
			}
		});

		//set the entity to DIRTY if on of the related associations get changed...!
	}

	private function shouldUseExtension(AdminInterface $admin)
	{
		return $admin->getSubject()
			&& $admin->getSubject()->getId()
			&& $admin->getSubject() instanceof LogExtraDataAware
		;
	}


	public function onAdminEditFormBottom(BlockEvent $event)
	{
		/** @var AdminInterface $admin */
		$admin = $event->getSetting('admin');
		if (!$this->shouldUseExtension($admin)) {
			return;
		}


		$block = new Block();
		$block->setType('core.loggable_entity.block.entity_log');
		$block->setSetting('subject_class', get_class($admin->getSubject()));
		$block->setSetting('subject_id', $admin->getSubject()->getId());
		$event->addBlock($block);
	}

	public function buildLogEntriesQueryForEntity($className, $id)
	{
		if (!$id) {
			throw new \InvalidArgumentException('ID parameter is not valid');
		}

//		$loggableEntryClassname = $this->loggableListener->getLogEntryClassnameForClass($this->entityManager, $className);
		$loggableEntryClassname = get_class(new LogEntry());

		/** @var LogEntryRepository $repository */
		$repository = $this->entityManager->getRepository($loggableEntryClassname);
		if (!$repository instanceof LogEntryRepository) {
			throw new InvalidConfigurationException('Invalid loggable repository set for '.$loggableEntryClassname);
		}

		return $repository
			->createQueryBuilder('log')
			->select('log')
			->andWhere('log.objectClass = :class')
			->andWhere('log.objectId = :id')
			->setParameter('class', $className)
			->setParameter('id', $id)
			->orderBy('log.version', 'ASC')
			->getQuery();
	}



}