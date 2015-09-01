<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Enum\ProjectStatus;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProjectAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('createdAt')
            ->add('description')
            ->add('status')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('client')
            ->add('name')
            ->add('status')
            ->add('createdAt')
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


        $formMapper->with('Project', array('class' => 'col-md-6'));

        if ('client' !== $parentAdmin) {
            $formMapper->add('client', 'sonata_type_model_list', array(), array());
        }

        $formMapper
            ->add('name')
            ->add('status', 'choice', array(
                'choices' => ProjectStatus::getChoices(),
            ))
        ;
        $formMapper->end();

        $formMapper->with('Info', array('class' => 'col-md-6'));
        $formMapper
            ->add('description', 'textarea', array(
                'required' => false,
            ))
        ;
        $formMapper->end();

        if (null === $parentAdmin) {
            $formMapper->with('Members', array('col-md-12'));
            $formMapper->add('memberships', 'sonata_type_collection', array(
                'label'         => false,
                'by_reference'  => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'link_parameters' => array(
                    'parent_admin'  => 'project',
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
            ->add('createdAt')
            ->add('description')
            ->add('status')
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
}
