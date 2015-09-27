<?php

namespace Application\CrmBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ContactInformationAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('companyPhone')
            ->add('privatePhone')
            ->add('companyEmail')
            ->add('privateEmail')
            ->add('skypeId')
            ->add('facebookId')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('companyPhone')
            ->add('privatePhone')
            ->add('companyEmail')
            ->add('privateEmail')
            ->add('skypeId')
            ->add('facebookId')
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
        $advancedFields = $parentAdmin !== 'lead';

        $formMapper->add('companyPhone');

        if ($advancedFields) $formMapper->add('privatePhone');
        $formMapper->add('companyEmail');
        if ($advancedFields) $formMapper->add('privateEmail');
        $formMapper->add('skypeId');
        if ($advancedFields) $formMapper->add('facebookId');

    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('companyPhone')
            ->add('privatePhone')
            ->add('companyEmail')
            ->add('privateEmail')
            ->add('skypeId')
            ->add('facebookId')
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
