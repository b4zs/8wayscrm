<?php

namespace Application\CrmBundle\Admin;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Application\CrmBundle\Entity\Address;
use Application\CrmBundle\Entity\Contact;
use Application\CrmBundle\Entity\AbstractClient;
use Application\CrmBundle\Enum\ClientStatus;
use Application\CrmBundle\Enum\Country;
use Application\CrmBundle\Form\CustomPropertyType;
use Application\UserBundle\Entity\User;

class ClientAdmin extends Admin
{


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('quicksearch', 'doctrine_orm_callback', array(
                'callback' => function($queryBuilder, $alias, $field, $value){
                    if(null !== $value['value']) {
                        $matchedOids = $this
                            ->getConfigurationPool()
                            ->getContainer()
                            ->get('doctrine.orm.default_entity_manager')
                            ->getRepository('ApplicationObjectIdentityBundle:ObjectIdentity')
                            ->createFulltextSearchQueryBuilder($value['value'])
                            ->andWhere('oid.type = :type')
                            ->setParameter('type', 'abstractclient')
                            ->getQuery()
                            ->getResult();

                        $oid = array();

                        foreach ($matchedOids as $matchedOid) {
                            $oid[] = $matchedOid['objectIdentity']->getId();
                        }

                        /** @var QueryBuilder $query */
                        $aliases = $queryBuilder->getRootAliases();
                        $queryBuilder->andWhere(sprintf('%s.objectIdentity IN (:oids)', current($aliases)));
                        $queryBuilder->setParameter('oids', $oid);
                    }

                }
            ), 'text', array(
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Quick search'
                )
            ))
            ->add('company', 'doctrine_orm_callback', array(
                'callback' => function($queryBuilder, $alias, $field, $value){
                    if(null !== $value['value']) {
                        $aliases = $queryBuilder->getRootAliases();
                        $queryBuilder
                            ->andWhere(current($aliases).'.company.name LIKE :company_filter')
                            ->setParameter('company_filter', '%'.$value['value'].'%');
                    }
                }
            ), 'text')
            ->add(
                'status',
                'doctrine_orm_choice',
                array(
                    'label' => 'Status', 'multiple' => true,
                ),
                'choice',
                array('choices' => ClientStatus::getChoices(), 'multiple' => true,)
            )
            ->add('owner')
            ->add('country', 'doctrine_orm_callback', array(
                'callback' => function($queryBuilder, $alias, $field, $value){
                    /** @var QueryBuilder $queryBuilder */
                    if(null !== $value['value']) {
                        $aliases = $queryBuilder->getRootAliases();
                        $queryBuilder->join(sprintf('%s.addresses', current($aliases)), 'country');
                        $queryBuilder
                            ->andWhere('country.country = :country')
                            ->setParameter('country', $value['value']);
                    }
                }
            ), 'country')
            ->add('city', 'doctrine_orm_callback', array(
                'callback' => function($queryBuilder, $alias, $field, $value){
                    /** @var QueryBuilder $queryBuilder */
                    if(null !== $value['value']) {
                        $aliases = $queryBuilder->getRootAliases();
                        $queryBuilder->join(sprintf('%s.addresses', current($aliases)), 'city');
                        $queryBuilder
                            ->andWhere('city.city = :city')
                            ->setParameter('city', $value['value']);
                    }
                }
            ), 'text')
            ->add('sectorOfActivity', 'doctrine_orm_callback', array(
                'callback' => function($queryBuilder, $alias, $field, $value){
                    /** @var QueryBuilder $queryBuilder */
                    if(null !== $value['value']) {
                        $aliases = $queryBuilder->getRootAliases();
                        $queryBuilder
                            ->andWhere(sprintf('%s.company.sectorOfActivity = :soa', current($aliases)))
                            ->setParameter('soa', $value['value']);
                    }
                }
            ), 'choice', array(
                'choices' => $this->buildSectorOfActivityChoices()
            ))
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
                'label' => $this->getClassnameLabel(),
            ))
            ->add('status', 'choice', array(
                'editable' => true,
                'choices'  => ClientStatus::getChoices(),
            ))
//            ->add('createdAt', null, array(
//                'label' => 'created',
//            ))
            ->add('updatedAt', null, array(
                'label' => 'Updated',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
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
            $formMapper->add('status', 'choice', array(
                'required'  => false,
                'choices'   => ClientStatus::getChoices(),
                'expanded'  => false,
                'multiple'  => false
            ));
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
            $formMapper->add('customProperties', 'sonata_type_native_collection', array(
                'type' => new CustomPropertyType(),
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'by_reference' => false
            ));
            /*$formMapper->add('financialInformation', 'textarea', array(
                'required'  => false,
                'attr'      => array('rows' => '9'),
            ));*/

        $formMapper->end();

        if($this->getSubject()->getContacts()->count() > 0) {
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
                'by_reference'          => true,
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
        $showMapper->with('Company', array('class' => 'col-md-7',));
        $showMapper
            ->add('company.name')
            ->add('company.sectorOfActivity', 'choice', array(
                'choices' => $this->buildSectorOfActivityChoices(),
            ))
            ->add('company.country')
            ->add('company.website')
            ->add('company.email')
            ->add('company.phone1')
            ->add('company.phone2')
            ->add('company.fax')
        ;
        $showMapper->end();

        $showMapper->with('Management', array('class' => 'col-md-5',));
        $showMapper->add('status', 'choice', array(
            'choices'   => ClientStatus::getChoices(),
        ));
        $showMapper->add('owner');
        $showMapper->add('projectManager');
        $showMapper->add('referral');
        $showMapper->add('financialInformation');
        $showMapper->end();


        $showMapper->with('Contact ', array('class' => 'col-md-12',));
        $showMapper->add('contacts');
        $showMapper->end();

        $showMapper->with('Addresses', array('class' => 'col-md-12',));
        $showMapper->add('addresses');
        $showMapper->end();

        $showMapper->with('Projects');
        $showMapper->add('projects');
        $showMapper->end();

        $showMapper->with('Files');
        $showMapper->add('fileset.galleryHasMedias');
        $showMapper->end();
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

    public function getTemplate($name)
    {
        switch (true) {
//            case $name === 'edit' && $this->getSubject() && null === $this->getSubject()->getId():
//                return 'ApplicationCrmBundle:ClientAdmin:create.html.twig';
//                break;
            case $name === 'edit' && $this->getSubject():
                return 'ApplicationCrmBundle:ClientAdmin:edit.html.twig';
                break;
            case $name === 'list':
                return 'ApplicationCrmBundle:ClientAdmin:list.html.twig';
                break;
            default:
                return parent::getTemplate($name);
        };
    }

    private function addCompanyFields(FormBuilderInterface $companyField)
    {
        $companyField
            ->add('name', null, array(
                'required'      => true,
                'constraints'   => array(
                    new NotBlank(),
                )
            ))
            ->add('sectorOfActivity', 'choice', array(
                'required' => false,
                'choices' => $this->buildSectorOfActivityChoices(),
            ))
            ->add('country', 'country', array(
                'required' => false,
                'preferred_choices' => Country::getPreferredChoices(),
            ))
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

    public function isGranted($name, $object = null)
    {

        return parent::isGranted($name, $object)
        && (
            (in_array($name, array('EDIT', 'SHOW', 'DELETE')) && $object)
            ? $this->getConfigurationPool()->getContainer()->get('application_crm.admin.extension.owner_group_manager')->isGranted($name, $object)
            : true
        );
    }

    private function buildSectorOfActivityChoices()
    {
        /** @var EntityRepository $repository */
        $repository = $this->configurationPool->getContainer()->get('doctrine')->getRepository('ApplicationCrmBundle:SectorOfActivity');
        $all = $repository
            ->createQueryBuilder('s')
            ->select('s.name')
            ->getQuery()
            ->execute();

        $choices = array();
        foreach ($all as $item) {
            $choices[$item['name']] = $item['name'];
        }

        return $choices;
    }

    public function getExportFields()
    {
        return array(
            'company.name',
            'status',
            'company.country',
            'company.website',
            'company.phone1',
            'company.phone2',
            'company.sectorOfActivity',
            'owner.fullName',
            'getFirstContact.person.gender',
            'getFirstContact.title',
            'getFirstContact.person.fullName',
            'getFirstContact.person.companyEmail',
            'getFirstContact.person.companyPhone',
        );
    }


}
