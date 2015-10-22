<?php

namespace Application\CrmBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProjectMembershipAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('project')
            ->add('role')
            ->add('user')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('project')
            ->add('role')
            ->add('user')
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

        if ($parentAdmin !== 'user') {
            $formMapper->add('user', 'sonata_type_model_list', array(
                'required'      => true,
                'btn_delete'    => false,
                'btn_add'       => false,
            ));
        }

        $formMapper->add('role', 'text', array(
            'required' => false,
        ));

        if ($parentAdmin !== 'project') {
            $formMapper->add('project', 'sonata_type_model_list', array(
                'required' => true,
            ));
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('role')
            ->add('createdAt')
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
