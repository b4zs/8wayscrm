<?php

namespace Application\QuotationGeneratorBundle\Admin;

use Application\CrmBundle\Entity\Project;
use Application\QuotationGeneratorBundle\Entity\FillOut;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class FillOutAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('createdAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('createdAt')
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
            ->add('name', 'text')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $self = $this;
        $showMapper
            ->add('id')
            ->add('name')
            ->add('state', null, array(
                'template' => 'ApplicationQuotationGeneratorBundle:Admin:state_show_field.html.twig',
                'callback' => function($value, $object) use($self) {
                    return $self->getConfigurationPool()->getContainer()->get('application_quotation_generator.fillout_manager')->updateFillOutState($object);
                },
            ))
            ->add('createdAt')
        ;
    }

    public function toString($object)
    {
        if ($object instanceof FillOut) {
            return $object->getName() ?: 'new';
        } else {
            return parent::toString($object);
        }
    }


    protected function configureTabMenu(ItemInterface $menu, $action, AdminInterface $activeChildAdmin = null)
    {
        if (!$this->hasSubject() || !$this->getSubject()->getId()) {
            return;
        }

        $request = $this->configurationPool->getContainer()->get('request_stack')->getCurrentRequest();
        $editRoute = $this->getBaseRouteName().'_'.'edit';

//        if ($activeChildAdmin) {
            $menu->addChild('Edit '.json_encode(substr($this->toString($this->getSubject()), 0, 15)), array(
                'uri' => $this->generateObjectUrl('edit', $this->getSubject()),
                'current' => $request->get('_route') == $editRoute,
            ));
//        }


        if (!$this->isChild()) {
            /** @var Admin $childItem */
            foreach ($this->getChildren() as $childAdmin) {
                if ($childAdmin instanceof FillOutAnswerAdmin) {
                    $menu->addChild($childAdmin->getClassnameLabel(), array(
                        'uri' => $childAdmin->generateUrl('list', array('id' => $this->getSubject()->getId())),
                        'current' => $childAdmin === $activeChildAdmin,
                    ));
                } else {
                    $menu->addChild($childAdmin->getLabel(), array(
                        'uri' => $childAdmin->generateUrl('list'),
                        'current' => $childAdmin === $activeChildAdmin,
                    ));
                }
            }
        }

        if ($this->getSubject()) {
            $router = $this->configurationPool->getContainer()->get('router');
            $route = $this->getBaseRouteName().'_'.'frontend';
            $menu->addChild('Frontend', array(
                'uri' => $router->generate($route, array('id' => $this->getSubject()->getId(), 'childId' => $this->getSubject()->getId())),
                'current' => $request->get('_route') == $route,
            ));
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('frontend', $this->getRouterIdParameter().'/frontend', array(), array('id' => '.+', '_method' => 'GET'));
    }

    public function getNewInstance()
    {
        /** @var FillOut $fillout */
        $fillout = parent::getNewInstance();

        if ($this->isChild() && $this->getParent()->getSubject() instanceof Project) {
            $project = $this->getParent()->getSubject();
            $fillout->setProject($project);
            $fillout->setName(sprintf('%s - %s', $project->getName(), date('Y-m-d H:i')));
        }

        return $fillout;
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        if ($this->isChild() && $this->getParent()->getSubject() instanceof Project) {
            $query->andWhere('o.project = :project');
            $query->setParameter('project', $this->getParent()->getSubject());
        }

        return $query;
    }


}
