<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Enum\AddressType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AddressAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('country')
            ->add('state')
            ->add('city')
            ->add('street')
            ->add('streetNumber')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('country')
            ->add('state')
            ->add('city')
            ->add('street')
            ->add('streetNumber')
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
        $formMapper->add('first', 'form', array(
            'label'         => 'Country/City',
            'inherit_data'  => true,
            'required'      => false,
        ));
        $formMapper->get('first')
            ->add('country', 'country', array(
                'required' => false,
            ))
            ->add('city', null, array(
                'required' => false,
            ))
            ->add('postalCode', null, array(
                'required' => false,
            ))
            ->add('postbox', null, array(
                'required' => false,
            ))
        ;

        $formMapper->add('second', 'form', array(
            'label'         => 'Street/number',
            'inherit_data'  => true,
            'required'      => false,
        ));
        $formMapper->get('second')
            ->add('type', 'choice', array(
                'required' => false,
                'choices'  => AddressType::getChoices(),
                'label'    => 'Address type',
                'required' => false,
            ))
            ->add('street', null, array(
                'required' => false,
            ))
            ->add('streetNumber', null, array(
                'required' => false,
            ))
//            ->add('name', null, array(
//                'required' => false,
//            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('country')
            ->add('state')
            ->add('city')
            ->add('street')
            ->add('streetNumber')
        ;
    }
}
