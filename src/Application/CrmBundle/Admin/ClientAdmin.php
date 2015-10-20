<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Entity\Address;
use Application\CrmBundle\Entity\Company;
use Application\CrmBundle\Entity\Contact;
use Application\CrmBundle\Entity\Individual;
use Application\CrmBundle\Entity\AbstractClient;
use Application\CrmBundle\Entity\Person;
use Application\CrmBundle\Entity\Project;
use Application\CrmBundle\Enum\ClientStatus;
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
            ->add('company', 'doctrine_orm_callback', array(
                'callback' => function($queryBuilder, $alias, $field, $value){
                    $aliases = $queryBuilder->getRootAliases();
                    $value = is_array($value) ? $value['value'] : null;
                    $queryBuilder
                        ->andWhere(current($aliases).'.company.name LIKE :company_filter')
                        ->setParameter('company_filter', '%'.$value.'%');
                }
            ))
//            ->add('financialInformation')
            ->add('status', null, array('label' => 'Status'), 'choice', array('choices' => ClientStatus::getChoices()))
            ->add('owner')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('owner')
            ->addIdentifier('company', null, array(
                'label' => 'Client',
            ))
            ->add('status')
//            ->add('createdAt', null, array(
//                'label' => 'created',
//            ))
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

        $formMapper->with('Addresses', array('class' => 'col-md-12',));
            $formMapper->add('addresses', 'sonata_type_collection', array(
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

        $formMapper->with('Files');
            $formMapper->add('fileset.galleryHasMedias', 'sonata_type_collection', array(
                'label'                 => false,
                'by_reference'          => false,
                'cascade_validation'    => true,
            ), array(
                'edit'              => 'inline',
                'inline'            => 'table',
                'sortable'          => 'position',
                'link_parameters'   => array('context' => 'default'),
                'admin_code'        => 'sonata.media.admin.gallery_has_media',
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
            ->add('financialInformation')
            ->add('status')
            ->add('createdAt')
        ;
    }

    public function getNewInstance()
    {
        /** @var AbstractClient $instance */
        $instance = parent::getNewInstance();

        $container = $this->configurationPool->getContainer();
        $securityContext = $container->get('security.context');
        $user = $securityContext->getToken() ? $securityContext->getToken()->getUser() : null;
        if ($user instanceof User) {
            $instance->setOwner($user);
        }

        $instance->addContact(new Contact());
        $instance->addAddress(new Address());
//        $instance->addProject(new Project());


        return $instance;
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);
    }

    /**
     * @return AbstractClient|null
     */
    public function getSubject()
    {
        return parent::getSubject(); // TODO: Change the autogenerated stub
    }

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

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        if ($object instanceof AbstractClient) {
            $object->setUpdatedAt(new \DateTime());
        }
    }

    public function getDefaultSortValues($class)
    {
        return array(
            '_sort_order' => 'DESC',
            '_sort_by'    => 'updatedAt',
            '_page'       => 1,
            '_per_page'   => 25,
        );
    }


}
