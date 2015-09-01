<?php

namespace Application\CrmBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CompanyAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
//            ->add('mainContactInformation')
//            ->add('sectorOfActivity')
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
        $parentAdmin = $this->getParentAdmin($formMapper);


        $formMapper->with('Company', array('class' => 'col-md-6',));
        $formMapper
            ->add('name')
            ->add('sectorOfActivity', 'text', array(
                'required'  => false,
            ))
        ;
        $formMapper->end();

        if ('client' !== $parentAdmin) {
            $formMapper->with('Contact', array('class' => 'col-md-6',));
            $formMapper
                ->add('mainContactInformation', 'sonata_type_admin', array(
                    'delete'    => false,
                    'btn_add'   => false,
                    'label'     => false,
                ))
            ;
            $formMapper->end();
        }

        if (null === $parentAdmin) {
            $formMapper->with('Members', array('class' => 'col-md-12',));
            $formMapper->add('memberships', 'sonata_type_collection', array(
                'label'         => false,
                'by_reference'  => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'link_parameters' => array(
                    'parent_admin'  => 'company',
                ),
            ));
            $formMapper->end();

            $formMapper->with('Offices', array('class' => 'col-md-12',));

            $formMapper->add('offices', 'sonata_type_collection', array(
                'label'         => false,
                'by_reference'  => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'link_parameters' => array(
                    'parent_admin'  => 'company',
                ),
            ));
            $formMapper->end();
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('mainContactInformation')
            ->add('sectorOfActivity')
        ;
    }

    protected function getParentAdmin(FormMapper $formMapper)
    {
        $options = $formMapper->getFormBuilder()->getFormConfig()->getOptions();
        if (isset($options['sonata_field_description'])) {
            $options = $options['sonata_field_description']->getOptions();
            $linkParameters = isset($options['link_parameters']) ? $options['link_parameters'] : array();
            $parentAdmin = isset($linkParameters['parent_admin']) ? $linkParameters['parent_admin'] : null;
        } else {
            $parentAdmin = null;
        }

        return $parentAdmin;
    }
}
