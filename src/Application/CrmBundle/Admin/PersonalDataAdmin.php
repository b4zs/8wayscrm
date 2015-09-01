<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Form\PersonalDataType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PersonalDataAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('firstName')
            ->add('lastName')
            ->add('dateOfBirth')
            ->add('gender')
            ->add('nationality')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('firstName')
            ->add('lastName')
            ->add('dateOfBirth')
            ->add('gender')
            ->add('nationality')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
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
	    $formMapper
		    ->add('firstName', 'text', array(
			    'required'  => false,
		    ))
		    ->add('lastName', 'text', array(
			    'required'  => false,
		    ))
		    ->add('dateOfBirth', 'date', array(
			    'widget'    => 'single_text',
			    'required'  => false,
		    ))
		    ->add('gender', 'choice', array(
			    'required'  => false,
			    'choices'   => array(
				    'm' => 'Male',
				    'f' => 'Female',
			    ),
		    ))
		    ->add('nationality', 'country', array(
			    'required'  => false,
		    ))
		    ->add('avatar', 'sonata_type_model_list', array(
			    'required'  => false,
		    ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('firstName')
            ->add('lastName')
            ->add('dateOfBirth')
            ->add('gender')
            ->add('nationality')
        ;
    }
}
