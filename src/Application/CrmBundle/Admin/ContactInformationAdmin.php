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
            ->add('companyPhone')
            ->add('privatePhone')
            ->add('companyEmail')
            ->add('privateEmail')
            ->add('skypeId')
            ->add('facebookId')
        ;
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
}
