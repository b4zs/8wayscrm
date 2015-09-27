<?php


namespace Application\CrmBundle\Controller;


use Application\CrmBundle\Admin\LeadAdmin;
use Application\CrmBundle\Enum\LeadType;
use Sonata\AdminBundle\Controller\CRUDController;

class LeadAdminController extends CRUDController
{
	/** @var $admin LeadAdmin */

	public function createAction()
	{
		$request = $this->container->get('request');

		$templateKey = 'edit';
		if (false === $this->admin->isGranted('CREATE')) {
			throw new AccessDeniedException();
		}

		$object = $this->admin->getNewInstance();

		$this->admin->setSubject($object);

		/** @var $form \Symfony\Component\Form\Form */
		$form = $this->admin->getForm();
		$form->setData($object);


		$type = $this->admin->getRequestTypeForCreate();

		if (!$type) {
			// the key used to lookup the template

			$types = LeadType::getChoices();
			return $this->render('ApplicationCrmBundle:LeadAdmin:create_type_select.html.twig', array(
				'types' => $types,
				'action' => 'create',
//				'form'   => $view,
				'object' => $object,
			));
		} else {
			return parent::createAction();
		}
	}

}