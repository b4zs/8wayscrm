<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Enum\ClientStatus;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ClientAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('type')
            ->add('financialInformation')
            ->add('status')
            ->add('createdAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
//            ->add('type')
//            ->add('financialInformation')
            ->add('status')
            ->add('company')
            ->add('createdAt')
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
//            ->add('status')
        ;


//        $formMapper->add('type');
        $formMapper->with('Company', array('class' => 'col-md-7',));
        $formMapper->add('company', 'sonata_type_model_list', array(
            'label' => false,
//            'btn_delete' => false,
//            'btn_add' => false,
        ), array(
//            'edit' => 'inline',
            'link_parameters' => array(
                'parent_admin' => 'client',
            )
        ));
        $formMapper->end();

        $formMapper->with('Information', array('class' => 'col-md-5',));
        $formMapper->add('status', 'choice', array(
            'required'  => false,
            'choices'   => ClientStatus::getChoices(),
        ));

        $formMapper->add('financialInformation', 'textarea', array(
            'required' => false,
        ));
        $formMapper->end();


        $formMapper->with('Contact Persons', array('class' => 'col-md-12',));

        $formMapper->add('contactPersons', 'sonata_type_collection', array(
            'label'         => false,
            'by_reference'  => false,
        ), array(
            'edit' => 'inline',
            'inline' => 'table',
            'link_parameters' => array(
                'parent_admin'  => 'client',
            ),
        ));
        $formMapper->end();

        $formMapper->with('Projects');
        $formMapper->add('projects', 'sonata_type_collection', array(
            'label'         => false,
            'by_reference'  => false,
        ), array(
            'edit' => 'inline',
            'inline' => 'table',
            'link_parameters' => array(
                'parent_admin'  => 'client',
            ),
        ));
        $formMapper->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('type')
            ->add('financialInformation')
            ->add('status')
            ->add('createdAt')
        ;
    }
}
