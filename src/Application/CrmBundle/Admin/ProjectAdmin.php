<?php

namespace Application\CrmBundle\Admin;

use Application\CrmBundle\Entity\Project;
use Application\CrmBundle\Enum\ProjectStatus;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class ProjectAdmin extends Admin
{
    /**
     * @param DatagridMapper $dataGridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $dataGridMapper)
    {
        $dataGridMapper
            ->add('name')
            ->add('parent')
            ->add(
                'status',
                'doctrine_orm_choice',
                array(
                    'label' => 'Status',
                    'multiple' => true,
                ),
                'choice',
                array('choices' => ProjectStatus::getChoices(), 'multiple' => true,)
            )
            ->add('client', 'doctrine_orm_callback', array(
                'callback' => function ($queryBuilder, $alias, $field, $value) {
                    $aliases = $queryBuilder->getRootAliases();
                    $value = is_array($value) ? $value['value'] : null;
                    if (!empty($value)) {
                        $queryBuilder
                            ->innerJoin(current($aliases).'.client', 'client')
                            ->andWhere('client.company.name LIKE :company_filter')
                            ->setParameter('company_filter', '%'.$value.'%');
                    }
                }
            ))
            ->add('id');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $this->setListMode('list');
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('client')
            ->add('status', 'choice', array(
                'editable' => true,
                'choices' => ProjectStatus::getChoices(),
            ))
            ->add('createdAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $parentAdmin = $this->getParentAdmin($formMapper);


        $formMapper->with('Project', array('class' => 'col-md-6'));
        $formMapper->add('name');
        $formMapper->add('parent', ModelListType::class, []);

        if ('lead' !== $parentAdmin) {
            $formMapper->add('client', 'sonata_type_model_list', array(
                'btn_delete' => false,
                'btn_list' => 'Select',
            ), array());
        }

        $formMapper->end();

        $formMapper->with('Info', array('class' => 'col-md-6'));
        $formMapper->add('status', 'choice', array(
            'choices' => ProjectStatus::getChoices(),
        ));
        $formMapper
            ->add('description', 'textarea', array(
                'required' => false,
            ));
        $formMapper->end();

        if (null === $parentAdmin) {
            $formMapper->with('Members', array('col-md-12'));
            $formMapper->add('memberships', 'sonata_type_collection', array(
                'label' => false,
                'by_reference' => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'link_parameters' => array(
                    'parent_admin' => 'project',
                ),
            ));
            $formMapper->end();

            $formMapper->with('Files');
            $formMapper->add('fileset.galleryHasMedias', 'sonata_type_collection', array(
                'label' => false,
                'by_reference' => false,
                'cascade_validation' => true,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
                'link_parameters' => array('context' => 'default'),
                'admin_code' => 'sonata.media.admin.gallery_has_media',
            ));
            $formMapper->end();
        } else {
            $disabled = !$this->getSubject() || !$this->getSubject()->getId();
            $formMapper->with('open');
            $formMapper->add('_open', 'gb_open_button',
                array('required' => false, 'mapped' => false, 'label' => 'Details', 'disabled' => $disabled));
            $formMapper->end();
        }


    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->with('Project', array('class' => 'col-md-7',));
        $showMapper
            ->add('id')
            ->add('name')
            ->add('createdAt')
            ->add('description')
            ->add('status')
        ;
        $showMapper->end();

        $showMapper->with('Memberships', array('class' => 'col-md-5',));
        $showMapper->add('memberships');
        $showMapper->end();

        $showMapper->with('Files', array('class' => 'col-md-5',));
        $showMapper->add('fileset.galleryHasMedias');
        $showMapper->end();

        $showMapper->with('Sub projects', array('class' => 'col-md-7',));
        $showMapper
            ->add('originalChildren', null, [
                'template' => 'ApplicationCrmBundle:ProjectAdmin:project_children.html.twig'
            ])
        ;
        $showMapper->end();




    }

    /**
     * @return null
     */
    protected function getParentAdmin(FormMapper $formMapper)
    {
        $options = $formMapper->getFormBuilder()->getFormConfig()->getOptions();
        if (isset($options['sonata_field_description'])) {
            $options = $options['sonata_field_description']->getOptions();
            $linkParameters = isset($options['link_parameters']) ? $options['link_parameters'] : array();
            $parentAdmin = isset($linkParameters['parent_admin']) ? $linkParameters['parent_admin'] : null;
            return $parentAdmin;
        } else {
            $parentAdmin = null;
            return $parentAdmin;
        }

        return $parentAdmin;
    }

    public function getTemplate($name)
    {
        switch (true) {
            case $name === 'edit' && $this->getSubject():
                return 'ApplicationCrmBundle:ProjectAdmin:edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
        };
    }

    public function isGranted($name, $object = null)
    {
        return parent::isGranted($name, $object)
            && (
            (in_array($name, array('EDIT', 'SHOW', 'DELETE')) && $object)
                ? $this->getConfigurationPool()->getContainer()->get('application_crm.admin.extension.owner_group_manager')->isGranted($name,
                $object)
                : true
            );
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('loadMoreProject');
        $collection->add('loadChildren');
    }

    public function getNewInstance()
    {
        /** @var Project $project */
        $project = parent::getNewInstance();
        $parentId = $this->getRequest()->get('parent_id');

        if (!$parentId) {
            return $project;
        }

        $parent = $this->modelManager->findOneBy(Project::class, [
            'id' => $parentId
        ]);

        if ($parent) {
            $project->setParent($parent);
            return $project;
        }
    }


}
