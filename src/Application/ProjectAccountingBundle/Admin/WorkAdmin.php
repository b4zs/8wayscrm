<?php

namespace Application\ProjectAccountingBundle\Admin;

use Application\ProjectAccountingBundle\Enum\WorkNature;
use Application\ProjectAccountingBundle\Enum\WorkStatus;
use Application\ProjectAccountingBundle\Enum\WorkTracker;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class WorkAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('tracker', null, array(),  'choice', array('choices' => WorkTracker::getChoices(), 'required' => false,))
            ->add('nature', null, array(),  'choice', array('choices' => WorkNature::getChoices(), 'required' => false,))
            ->add('status', null, array(),  'choice', array('choices' => WorkStatus::getChoices(), 'required' => false,))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('project', 'entity')
            ->add('name')
            ->add('tracker', 'choice', array('choices' => WorkTracker::getChoices(), 'required' => false,))
            ->add('nature', 'choice', array('choices' => WorkNature::getChoices(), 'required' => false,))
            ->add('status', 'choice', array('choices' => WorkStatus::getChoices(), 'required' => false,))
            ->add('initialEstimatedTime')
//            ->add('currentlyEstimatedTime')
            ->add('deadline')
//            ->add('createdAt')
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
            ->add('project', null, array('required' => true,))
            ->add('name', null, array('required' => true,))
            ->add('description', null, array('required' => false,))
            ->add('tracker', 'choice', array('choices' => WorkTracker::getChoices(), 'required' => false,))
            ->add('nature', 'choice', array('choices' => WorkNature::getChoices(), 'required' => false,))
            ->add('status', 'choice', array('choices' => WorkStatus::getChoices(), 'required' => false,))
            ->add('initialEstimatedTime', null, array('required' => false,))
            ->add('currentlyEstimatedTime', null, array('required' => false,))
            ->add('hourlyRate',  'accounting_price',  array('required' => false,))
            ->add('deadline', 'sonata_type_date_picker', array('required' => false,))
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
            ->add('tracker')
            ->add('nature')
            ->add('status')
            ->add('initialEstimatedTime')
            ->add('currentlyEstimatedTime')
            ->add('hourlyRate')
            ->add('deadline')
            ->add('createdAt')
        ;
    }
}
