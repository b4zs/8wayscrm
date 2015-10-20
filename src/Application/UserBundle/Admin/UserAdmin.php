<?php

namespace Application\UserBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;

class UserAdmin extends BaseUserAdmin
{
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->with('General', array('class' => 'col-md-6'))
			->add('username')
			->add('email')
			->add('plainPassword', 'text', array(
				'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
				'label'    => 'New password',
			))
			->end();

		$formMapper
			->with('Profile', array('class' => 'col-md-6'))
				->add('firstname', 'text', array('required' => false))
				->add('lastname', 'text', array('required' => false))
				->add('locale', 'locale', array('required' => false))
				->add('timezone', 'timezone', array('required' => false))
			->end()
		;

		if ($this->isGranted('ROLE_SUPER_ADMIN')) {
			$formMapper
				->with('Groups', array('class' => 'col-md-6'))
				->add('groups', 'sonata_type_model', array(
					'required' => false,
					'expanded' => true,
					'multiple' => true
				))
				->end();
		}
	}
}