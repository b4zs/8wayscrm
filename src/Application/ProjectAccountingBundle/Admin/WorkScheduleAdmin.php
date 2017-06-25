<?php

namespace Application\ProjectAccountingBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class WorkScheduleAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('user')
            ->add('scheduleDate')
            ->add('scheduleDuration')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('user')
            ->add('name')
            ->add('work')
            ->add('scheduleDate', 'date')
            ->add('scheduleDuration', 'number')
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
            ->add('work', null, array('required' => true,))
            ->add('name', null, array('required' => true,))
            ->add('description', null, array('required' => false,))
            ->add('user', null, array('required' => false,))
            ->add('scheduleDate', 'sonata_type_date_picker', array('required' => true,))
            ->add('scheduleDuration', 'number')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('scheduleDate')
            ->add('scheduleDuration')
            ->add('createdAt')
        ;
    }
}
