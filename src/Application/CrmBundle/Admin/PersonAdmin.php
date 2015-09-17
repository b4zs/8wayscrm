<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Form\PersonalDataType;
use Application\CrmBundle\Form\PersonType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PersonAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('personalData.firstName')
            ->add('personalData.lastName')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('personalData.firstName', null, array(
                'label' => 'firstname'
            ))
            ->add('personalData.lastName', null, array(
                'label' => 'last    name'
            ))
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
        $formMapper->with('Person', array('class' => 'col-md-6',));

        $formMapper->add('personalData', 'sonata_type_admin', array(
            'delete'    => false,
            'btn_add'   => false,
            'label'     => false,
        ));

        $formMapper->add('user', 'sonata_type_model_list', array(
            'label'     => 'User account',
        ));

        $formMapper->end();
        $formMapper->with('Contact', array('class' => 'col-md-6',));
        $formMapper->add('contactInformation', 'sonata_type_admin', array(
            'delete'    => false,
            'btn_add'   => false,
            'label'     => false,
        ));


        $formMapper->end();

        if ('client' === $this->getParentAdmin($formMapper)) {
            $formMapper->get('personalData')->remove('dateOfBirth');
            $formMapper->get('personalData')->remove('avatar');
            $formMapper->get('contactInformation')->remove('skypeId');
            $formMapper->get('contactInformation')->remove('facebookId');
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
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
        } else {
            $parentAdmin = null;
        }

        return $parentAdmin;
    }
}
