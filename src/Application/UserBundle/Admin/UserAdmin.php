<?php

namespace Application\UserBundle\Admin;

use Application\CrmBundle\Enum\WorkPermit;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;
use Symfony\Component\Intl\Intl;

class UserAdmin extends BaseUserAdmin
{
	protected function configureFormFields(FormMapper $formMapper)
	{
		$formMapper
			->with('General', array('class' => 'col-md-12'))
			->add('username')
			->add('email')
			->add('plainPassword', 'text', array(
				'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
				'label'    => 'New password',
			))
			->end();


		$formMapper->with('Basic info', array('class' => 'col-md-12'))
			->add('gender', 'sonata_user_gender', array(
				'required' => true,
				'translation_domain' => $this->getTranslationDomain()
			))
			->add('title', 'text', array('label' => 'Title', 'required' => false))
			->add('dateOfBirth', 'birthday', array('required' => false, 'widget' => 'single_text',))
			->add('firstname', null, array('required' => false))
			->add('lastname', null, array('required' => false))


		->end();

		$formMapper->with('Formal', array('class' => 'col-md-12'))
			->add('nationality', 'choice', array('label' => 'Nationality (language)', 'required' => false,'choices' => Intl::getLanguageBundle()->getLanguageNames()))
			->add('workPermit', 'choice', array('label' => 'Work Permit', 'required' => false, 'choices' => WorkPermit::getChoices()))
		->end();

		$formMapper->with('Contact', array('class' => 'col-md-12'))
			->add('privateEmail',       'text', array('label' =>'Private Email',      'required' => false))
			->add('workLine',           'text', array('label' =>'Work Line',          'required' => false))
			->add('workMobileLine',     'text', array('label' =>'Work Mobile Line',    'required' => false))
			->add('privateHomeLine',    'text', array('label' =>'Private Home Line',   'required' => false))
			->add('privateMobileLine',  'text', array('label' =>'Private Mobile Line', 'required' => false))
		->end();

		$formMapper->with('Private', array('class' => 'col-md-12'))
			->add('privateAddress',     'text', array('label' =>'Private Address',    'required' => false))
			->add('holidaysRemaining',  'text', array('label' =>'Holidays Remaining', 'required' => false))
		->end();


		$formMapper->with('Social', array('class' => 'col-md-12'))
			->add('facebookUid', null, array('required' => false))
			->add('facebookName', null, array('required' => false))
			->add('twitterUid', null, array('required' => false))
			->add('twitterName', null, array('required' => false))
			->add('gplusUid', null, array('required' => false))
			->add('gplusName', null, array('required' => false))
		->end();

		$formMapper->with('Documents', array('class' => 'col-md-12',));
		$formMapper->add('fileset.galleryHasMedias', 'sonata_type_collection', array(
			'label'                 => false,
			'by_reference'          => false,
			'cascade_validation'    => true,
		), array(
			'edit'              => 'inline',
			'inline'            => 'table',
			'sortable'          => 'position',
			'link_parameters'   => array('context' => 'default'),
			'admin_code'        => 'sonata.media.admin.gallery_has_media',
		));
		$formMapper->end();



		if ($this->isGranted('ROLE_SUPER_ADMIN')) {
			$formMapper
				->with('Groups', array('class' => 'col-md-12'))
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
				->add('primaryGroup', null, array('required' => false, 'label' => 'Primary group'))
				->end();
		} else {
			$formMapper->remove('groups');
		}


		if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN') && $this->isGranted('ROLE_SUPER_ADMIN')) {
			$formMapper
				->with('Management', array('class' => 'col-md-12'))
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

		if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
			$listMapper
				->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
			;
		}
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