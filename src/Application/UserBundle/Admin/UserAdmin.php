<?php

namespace Application\UserBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
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


		$formMapper->with('Profile', array('class' => 'col-md-6'))
			->add('dateOfBirth', 'birthday', array('required' => false, 'widget' => 'single_text',))
			->add('firstname', null, array('required' => false))
			->add('lastname', null, array('required' => false))
			->add('website', 'url', array('required' => false))
			->add('biography', 'text', array('required' => false))
			->add('gender', 'sonata_user_gender', array(
				'required' => true,
				'translation_domain' => $this->getTranslationDomain()
			))
			->add('locale', 'locale', array('required' => false))
			->add('timezone', 'timezone', array('required' => false))
			->add('phone', null, array('required' => false))
		->end();



		$formMapper->with('Social', array('class' => 'col-md-6'))
			->add('facebookUid', null, array('required' => false))
			->add('facebookName', null, array('required' => false))
			->add('twitterUid', null, array('required' => false))
			->add('twitterName', null, array('required' => false))
			->add('gplusUid', null, array('required' => false))
			->add('gplusName', null, array('required' => false))
		->end();


		if ($this->isGranted('ROLE_SUPER_ADMIN')) {
			$formMapper
				->with('Groups', array('class' => 'col-md-6'))
//				->add('groups', 'sonata_type_collection', array(
//					'required'  => false,
//					'label'     => false,
//				 	'expanded' => true,
//					'multiple' => true
//				))
				->add('groups', 'entity', array(
					'required' => false,
					'expanded' => false,
					'multiple' => true,
					'class' => 'Application\UserBundle\Entity\Group',
					'attr' => array(
						'style' => 'width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;',
					),
				))
				->add('primaryGroup', null, array('required' => true, 'label' => 'Primary group'))
				->end();
		} else {
			$formMapper->remove('groups');
		}


		if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN') && $this->isGranted('ROLE_SUPER_ADMIN')) {
			$formMapper
				->with('Management', array('class' => 'col-md-6'))
				->add('realRoles', 'sonata_security_roles', array(
					'label'    => false,
					'expanded' => true,
					'multiple' => true,
					'required' => false
				))
				->add('locked', null, array('required' => false))
				->add('expired', null, array('required' => false))
				->add('enabled', null, array('required' => false))
				->add('credentialsExpired', null, array('required' => false))
				->end()
			;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->addIdentifier('username')
			->add('email')
			->add('firstName', null, array('label' => 'First name'))
			->add('lastName', null, array('label' => 'Last name'))
			->add('enabled', null, array('editable' => true))
			->add('locked', null, array('editable' => true))
			->add('createdAt')
		;
	}

	public function toString($object)
	{
		if (!is_object($object)) {
			return '';
		}

		if (method_exists($object, '__toString') && null !== $object->__toString()) {
			return (string) $object;
		}

		return 'New';
	}

	public function isGranted($name, $object = null)
	{
		return parent::isGranted($name, $object); // TODO: Change the autogenerated stub
	}


}