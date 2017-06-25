<?php

namespace Application\ProjectAccountingBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class InvoiceAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('description')
            ->add('issuedAt')
            ->add('dueDate')
            ->add('status')
            ->add('createdAt')
            ->add('total.amount')
            ->add('total.currency')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('description')
            ->add('issuedAt')
            ->add('dueDate')
            ->add('status')
            ->add('createdAt')
            ->add('total.amount')
            ->add('total.currency')
            ->add('_action', null, array(
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
            ->add('id')
            ->add('description')
            ->add('issuedAt')
            ->add('dueDate')
            ->add('status')
            ->add('createdAt')
            ->add('total.amount')
            ->add('total.currency')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('description')
            ->add('issuedAt')
            ->add('dueDate')
            ->add('status')
            ->add('createdAt')
            ->add('total.amount')
            ->add('total.currency')
        ;
    }
}
