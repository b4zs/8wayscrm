<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Entity\Company;
use Application\CrmBundle\Entity\Contact;
use Application\CrmBundle\Entity\Individual;
use Application\CrmBundle\Entity\Client;
use Application\CrmBundle\Entity\Person;
use Application\CrmBundle\Entity\Project;
use Application\CrmBundle\Enum\ClientStatus;
use Application\CrmBundle\Enum\ClientType;
use Application\UserBundle\Entity\User;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class ClientAdmin extends Admin
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
            ->add('type', null, array('label' => 'Type'), 'choice', array('choices' => ClientType::getChoices()))
            ->add('status', null, array('label' => 'Status'), 'choice', array('choices' => ClientStatus::getChoices()))
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

        $formMapper->with('Company', array('class' => 'col-md-7',));
            $formMapper->add('company', 'form', array(
                'data_class'    => 'Application\\CrmBundle\\Entity\\Company',
                'label'         => false,
            ));
            $this->addCompanyFields($formMapper->get('company'));
        $formMapper->end();

        $formMapper->with('Management', array('class' => 'col-md-5',));
            $formMapper->add('owner', 'sonata_type_model_list', array(
                'required'  => false,
                'btn_add'   => false,
                'btn_delete'=> 'Unlink',
                'btn_list'  => 'Select',
            ));
            $formMapper->add('projectManager', 'sonata_type_model_list', array(
                'required'  => false,
                'btn_add'   => false,
                'btn_delete'=> 'Unlink',
                'btn_list'  => 'Select',
            ));
            $formMapper->add('referral', 'text', array(
                'required'  => false,
            ));
            $formMapper->add('financialInformation', 'textarea', array(
                'required'  => false,
                'attr'      => array('rows' => '9'),
            ));
            $formMapper->add('status', 'choice', array(
                'required'  => false,
                'choices'   => ClientStatus::getChoices(),
            ));
        $formMapper->end();


        if (ClientType::CLIENT === $type || ClientType::SUPPLIER === $type) {
            $formMapper->with('Contact ', array('class' => 'col-md-12',));
            $formMapper->add('contacts', 'sonata_type_collection', array(
                'label'         => false,
                'by_reference'  => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'link_parameters' => array(
                    'parent_admin'  => 'client',
                ),
            ));
            $formMapper->end();
        }

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
        /** @var Client $instance */
        $instance = parent::getNewInstance();

        $container = $this->configurationPool->getContainer();
        $securityContext = $container->get('security.context');
        $user = $securityContext->getToken() ? $securityContext->getToken()->getUser() : null;
        if ($user instanceof User) {
            $instance->setOwner($user);
        }

        if ($this->getRequest()) {
            if ($type = $this->getRequestTypeForCreate()) {
                $instance->setType($type);
            }
        }

        $instance->addContact(new Contact());
//        $instance->addProject(new Project());


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
     * @return Client|null
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
//            case $name === 'edit' && $this->getSubject() && null === $this->getSubject()->getId():
//                return 'ApplicationCrmBundle:ClientAdmin:create.html.twig';
//                break;
            case $name === 'edit' && $this->getSubject():
                return 'ApplicationCrmBundle:ClientAdmin:edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
        };
    }

    private function addCompanyFields(FormBuilderInterface $companyField)
    {
//        var_dump($companyField);die;

        $companyField
            ->add('name')
            ->add('sectorOfActivity', null, array('required' => false))
            ->add('country', 'country', array('required' => false))
            ->add('website', null, array('required' => false))
            ->add('email', null, array('required' => false))
            ->add('phone1', null, array('required' => false, 'label' => 'Line1',))
            ->add('phone2', null, array('required' => false, 'label' => 'Line2',))
            ->add('fax', null, array('required' => false))
        ;
    }


}
