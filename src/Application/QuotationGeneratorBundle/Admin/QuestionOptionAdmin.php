<?php

namespace Application\QuotationGeneratorBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class QuestionOptionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('text')
            ->add('value')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('text')
            ->add('value')
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
        $formMapper
            ->with('Choice')
            ->add('text')
            ->add('value')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('text')
            ->add('value')
            ->add('createdAt')
            ->add('deletedAt')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $i = parent::getNewInstance();
        if ($this->isChild()) {
            $i->setQuestion($this->getParent()->getSubject());
        }

        return $i;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
	    if ($this->isChild()) {
		    $query
			    ->andWhere('o.question = :p')
			    ->setParameter('p', $this->getParent()->getSubject());
	    }


	    return $query;
    }

    public function getClassnameLabel()
    {
        return 'Choice';
    }
}
