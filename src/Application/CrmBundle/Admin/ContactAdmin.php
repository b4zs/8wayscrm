<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Entity\AbstractClient;
use Application\CrmBundle\Entity\Contact;
use Application\CrmBundle\Enum\Country;
use Application\CrmBundle\Enum\PersonTitleEnum;
use Application\UserBundle\Enum\Gender;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ContactAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('person.firstName')
            ->add('person.lastName')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
	        ->add('person.fullName')
	        ->add('client.company')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $parentAdmin = $this->getParentAdmin($formMapper);

	    $formMapper->with('Personal', array('class' => 'col-md-6'));
		    $formMapper->add('personal', 'form', array(
			    'data_class'    => null,
			    'inherit_data'  => true,
			    'label'         => $parentAdmin ? 'Personal' : false,
			    'required'      => false,
		    ));

	        $formMapper->get('personal')
                ->add('gender', 'choice', array(
                    'property_path' => 'person.gender',
                    'choices'       => Gender::getChoices(),
                    'required'      => true,
                ))
		        ->add('title', 'choice', array(
			        'required'      => false,
                    'choices'       => PersonTitleEnum::getChoices()
		        ))
		        ->add('firstName', 'text', array(
					'property_path' => 'person.firstName',
			        'required'      => true,
				))
				->add('lastName', 'text', array(
					'property_path' => 'person.lastName',
					'required'      => false,
				))
				->add('dateOfBirth', 'date', array(
					'property_path' => 'person.dateOfBirth',
					'widget'        => 'single_text',
					'required'      => false,
				))
				/*->add('nationality', 'country', array(
					'property_path'     => 'person.nationality',
					'required'          => false,
					'preferred_choices' => Country::getPreferredChoices(),
				))*/
            ;
		$formMapper->end();

	    $formMapper->with('Contact', array('class' => 'col-md-6'));
		    $formMapper->add('contact', 'form', array(
			    'data_class'    => null,
			    'inherit_data'  => true,
			    'label'         => $parentAdmin ? 'Contact' : false,
			    'required'      => false,
		    ));
	        $formMapper->get('contact')
				->add('companyEmail', 'text', array(
					'property_path' => 'person.companyEmail',
					'label'         => 'Email (1)',
					'required'      => false,
				))
				->add('privateEmail', 'text', array(
					'property_path' => 'person.privateEmail',
					'label'         => 'Email (2)',
					'required'      => false,
				))
				/*->add('directLinePhone', 'text', array(
					'property_path' => 'person.directLinePhone',
					'label'         => 'Phone (line)',
					'required'      => false,
				))*/
				->add('companyPhone', 'text', array(
					'property_path' => 'person.companyPhone',
					'label'         => 'Phone (Pro)',
					'required'      => false,
				))
				/*->add('privatePhone', 'text', array(
					'property_path' => 'person.privatePhone',
					'label'         => 'Phone (Pte)',
					'required'      => false,
				))*/
				->add('skypeId', 'text', array(
					'property_path' => 'person.skypeId',
					'required'      => false,
				));
        $formMapper->end();



	    if (!$parentAdmin) {
		    $formMapper->with('Social', array('class' => 'col-md-6'));
		    $formMapper->add('social', 'form', array(
			    'data_class'    => null,
			    'inherit_data'  => true,
			    'label'         => $parentAdmin ? 'Social' : false,
			    'required'      => false,
		    ));
		    $formMapper->get('social')
					->add('facebookId', 'text', array(
						'property_path' => 'person.facebookId',
						'required'      => false,
					))
					->add('twitter', 'text', array(
						'property_path' => 'person.twitter',
						'required'      => false,
					))
					->add('instagram', 'text', array(
						'property_path' => 'person.instagram',
						'required'      => false,
					))
				;
		    $formMapper->end();
	    }


	    $formMapper->with('Relation', array('class' => 'col-md-6'));
	    $formMapper->add('relation', 'form', array(
		    'data_class'    => null,
		    'inherit_data'  => true,
		    'label'         => $parentAdmin ? 'Relation' : false,
		    'required'      => false,
	    ));
	    $formMapper->get('relation')
	        ->add('role', 'text', array(
			    'required'  => false,
		    ))
		    ->add('note', 'textarea', array(
			    'required'  => false,
			    'attr'      => array('rows' => 15,),
		    ));

            if ($parentAdmin !== 'client') {
                $formMapper->add('client');
            }
	    $formMapper->end();

    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('startDate')
            ->add('endDate')
            ->add('workPermit')
            ->add('holidaysRemaining')
        ;
    }

	/**
	 * @return null
	 */
	protected function getParentAdmin(FormMapper $formMapper)
	{
		$options = $formMapper->getFormBuilder()->getFormConfig()->getOptions();
		if (isset($options['sonata_field_description'])) {
			$options = $options['sonata_field_description']->getOptions();
			$linkParameters = isset($options['link_parameters']) ? $options['link_parameters'] : array();
			$parentAdmin = isset($linkParameters['parent_admin']) ? $linkParameters['parent_admin'] : null;
			return $parentAdmin;
		} else {
			$parentAdmin = null;
			return $parentAdmin;
		}

		return $parentAdmin;
	}

	public function isGranted($name, $object = null)
	{
		return parent::isGranted($name, $object)
		&& (
			(in_array($name, array('EDIT', 'SHOW', 'DELETE')) && $object)
			? $this->getConfigurationPool()->getContainer()->get('application_crm.admin.extension.owner_group_manager')->isGranted($name, $object)
			: true
		);
	}
}
