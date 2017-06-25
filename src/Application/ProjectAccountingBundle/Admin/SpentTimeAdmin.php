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
            ->add('startDate', 'sonata_type_date_picker', array())
            ->add('duration', 'number', array())
            ->add('description', null, array('required' => false,))
            ->add('user')
            ->add('work')
            ->add('project')
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
