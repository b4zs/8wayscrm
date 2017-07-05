<?php

namespace Application\ProjectAccountingBundle\Admin;

use Application\ProjectAccountingBundle\Entity\SpentTime;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SpentTimeAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('startDate')
            ->add('duration')
            ->add('description')
            ->add('createdAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('user')
            ->add('startDate', 'date')
            ->add('duration')
            ->add('project')
            ->add('work')
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
            ->with('Task', array('class' => 'col-md-6'))
            ->add('project')
            ->add('work')
            ->end()
            ->with('Log', array('class' => 'col-md-6'))
            ->add('user')
            ->add('startDate', 'sonata_type_date_picker', array())
            ->add('duration', 'number', array())
            ->end()
            ->with('Notes', array('class' => 'col-md-12'))
            ->add('description', null, array('required' => false,))
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('startDate')
            ->add('duration')
            ->add('description')
            ->add('createdAt')
        ;
    }

    public function getNewInstance()
    {
        /** @var SpentTime $object */
        $object = parent::getNewInstance();

        $object->setUser($this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser());

        return $object;
    }

    public function prePersist($object)
    {
        /** @var SpentTime $object */
        parent::prePersist($object);

        if ($object->getWork() && !$object->getProject()) {
            $object->setProject($object->getWork()->getProject());
        }
    }


}
