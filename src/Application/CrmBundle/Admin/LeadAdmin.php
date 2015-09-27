<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Entity\Company;
use Application\CrmBundle\Entity\Individual;
use Application\CrmBundle\Entity\Lead;
use Application\CrmBundle\Entity\Person;
use Application\CrmBundle\Enum\LeadStatus;
use Application\CrmBundle\Enum\LeadType;
use Application\UserBundle\Entity\User;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\HttpFoundation\Request;

class LeadAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
//            ->add('id')
//            ->add('type')
//            ->add('financialInformation')
            ->add('type', null, array('label' => 'Type'), 'choice', array('choices' => LeadType::getChoices()))
            ->add('status', null, array('label' => 'Status'), 'choice', array('choices' => LeadStatus::getChoices()))
//            ->add('createdAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('owner')
            ->add('type')
            ->add('status')
            ->add('company')
            ->add('createdAt', null, array(
                'label' => 'created',
            ))
            ->add('updatedAt', null, array(
                'label' => 'updated',
            ))
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
        $type = $this->getSubject() ? $this->getSubject()->getType() : null;

        if (LeadType::COMPANY === $type || LeadType::SUPPLIER === $type) {
            $formMapper->with(ucfirst($type) . ' information', array('class' => 'col-md-7',));

            $formMapper->add('company', 'sonata_type_admin', array(
                'label' => false,
                'btn_delete' => false,
                'btn_add' => false,
            ), array(
    //            'edit' => 'inline',
                'link_parameters' => array(
                    'parent_admin' => 'lead',
                )
            ));

            $formMapper->end();
        } elseif (LeadType::INDIVIDUAL === $type) {
            $formMapper->with('Individual information', array('class' => 'col-md-7',));

            $formMapper->add('individual', 'sonata_type_admin', array(
                'label' => false,
                'btn_delete' => false,
                'btn_add' => false,
            ), array(
                //            'edit' => 'inline',
                'link_parameters' => array(
                    'parent_admin' => 'lead',
                )
            ));

            $formMapper->end();
        }



        $formMapper->with('Lead', array('class' => 'col-md-5',));
        $formMapper->add('type', 'choice', array(
            'choices'   => LeadType::getChoices(),
            'expanded'  => true,
            'attr'      => array(
                'class' => ' radio-inline list-inline btn-group',
            ),
        ));
        $formMapper->add('status', 'choice', array(
            'required'  => false,
            'choices'   => LeadStatus::getChoices(),
        ));
        $formMapper->add('financialInformation', 'textarea', array(
            'required'  => false,
            'attr'      => array('rows' => '3'),
        ));
        $formMapper->add('owner', 'sonata_type_model_list', array(
            'required'  => false,
            'btn_add'   => false,
            'btn_delete'=> false,
            'btn_list'  => 'Select',
        ));
        $formMapper->end();

        $formMapper->with('Projects');
        $formMapper->add('projects', 'sonata_type_collection', array(
            'label'         => false,
            'by_reference'  => false,
        ), array(
            'edit' => 'inline',
            'inline' => 'table',
            'link_parameters' => array(
                'parent_admin'  => 'lead',
            ),
        ));
        $formMapper->end();

        if (LeadType::INDIVIDUAL !== $type) {
            $formMapper->with('Contact Persons', array('class' => 'col-md-12',));
            $formMapper->add('contactPersons', 'sonata_type_collection', array(
                'label'         => false,
                'by_reference'  => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'link_parameters' => array(
                    'parent_admin'  => 'lead',
                ),
            ));
            $formMapper->end();
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('type')
            ->add('financialInformation')
            ->add('status')
            ->add('createdAt')
        ;
    }

    public function getNewInstance()
    {
        /** @var Lead $instance */
        $instance = parent::getNewInstance();

        $container = $this->configurationPool->getContainer();
        $securityContext = $container->get('security.context');
        $user = $securityContext->getToken() ? $securityContext->getToken()->getUser() : null;
        if ($user instanceof User) {
            if ($user->getPerson() instanceof Person) {
                $instance->setOwner($user->getPerson());
            }
        }

        if ($this->getRequest()) {
            if ($type = $this->getRequestTypeForCreate()) {
                $instance->setType($type);
            }
        }


        return $instance;
    }

    public function getRequestTypeForCreate()
    {
        if ($this->getRequest() instanceof Request) {
            $type = $this->getRequest()->get('type');
            if (!$type) {
                $uniqid = $this->getUniqid();
                $type = $this->getRequest()->request->get($uniqid.'[type]', null, true);
            }

            return $type;
        };

        return null;
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);
    }

    /**
     * @return Lead|null
     */
    public function getSubject()
    {
        return parent::getSubject(); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        switch (true) {
            case $name === 'edit' && $this->getSubject() && null === $this->getSubject()->getId():
                return 'ApplicationCrmBundle:LeadAdmin:create.html.twig';
                break;
            case $name === 'edit' && $this->getSubject():
                return 'ApplicationCrmBundle:LeadAdmin:edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
        };
    }


}
