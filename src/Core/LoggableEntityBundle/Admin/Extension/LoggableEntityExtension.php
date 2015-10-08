<?php

namespace Core\LoggableEntityBundle\Admin\Extension;

use Application\CrmBundle\Entity\Client;
use Core\LoggableEntityBundle\Model\LogExtraData;
use Core\LoggableEntityBundle\Model\LogExtraDataAware;
use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class LoggableEntityExtension extends AdminExtension
{
	public function configureFormFields(FormMapper $form)
	{
		if (!$form->getAdmin()->getSubject()
			|| !$form->getAdmin()->getSubject()->getId()
			|| !$form->getAdmin()->getSubject() instanceof LogExtraDataAware) {
			return;
		}

		$form->getAdmin()->getSubject()->setLogExtraData(new LogExtraData());

		$form->with('Entity log');

		//TODO: get these values for the specific object type
		$form->add('log_custom_action', 'choice', array(
			'required' => false,
			'choices'  => array(
				'phone call'    => 'phone call',
				'email'         => 'email',
				'meeting'       => 'meeting',
			),
			'property_path' => 'logExtraData.customAction',
		));
		$form->add('log_extra_comment', 'textarea', array(
			'required' => false,
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


}