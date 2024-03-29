<?php

namespace Application\QuotationGeneratorBundle\Admin;

use Application\QuotationGeneratorBundle\Entity\Question;
use Application\QuotationGeneratorBundle\Enum\FormType;
use Application\QuotationGeneratorBundle\Enum\RequiredUserRole;
use Application\QuotationGeneratorBundle\Enum\Stage;
use Doctrine\Common\Util\ClassUtils;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\MenuItemInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class QuestionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('text')
            ->add('formType')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
//            ->add('category', null, array(
//                'editable' => true,
//            ))
            ->add('group')
            ->add('tags')
            ->addIdentifier('text')
            ->addIdentifier('alias')
            ->add('formType')
            ->add('stage', null, array(
                'template' => 'ApplicationQuotationGeneratorBundle:Admin:enum_field.html.twig',
                'choices' => Stage::getChoices(),
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
        $formMapper
            ->add('category', null, array('required' => false))
            ->add('group', null, array('required' => false))
            ->add('text', null, array('required' => false))
            ->add('hint', 'textarea', array('required' => false,))
            ->add('alias', null, array('required' => false))
            ->add('formType', 'choice', array(
                'choices' => FormType::getChoices(),
            ))
            ->add('requiredUserRole', 'choice', array(
                'choices' => RequiredUserRole::getChoices(),
                'required' => false,
            ))
            ->add('stage', 'choice', array(
                'choices' => Stage::getChoices(),
                'required' => false,
            ))
            ->add('tags', 'app_classification_tags', array(
                'required' => false,
            ))
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
            ->add('formType')
            ->add('createdAt')
            ->add('deletedAt')
        ;
    }

    /**
     * @return Question
     */
    public function getSubject()
    {
        return parent::getSubject(); // TODO: Change the autogenerated stub
    }


    protected function configureTabMenu(ItemInterface $menu, $action, AdminInterface $activeChildAdmin = null)
    {
        if (!$this->hasSubject() || null === $this->getSubject()->getId()) {
            return;
        }

        if ($activeChildAdmin) {
            $menu->addChild('Edit '.json_encode(substr($this->toString($this->getSubject()), 0, 15)), array(
                'uri' => $this->generateObjectUrl('edit', $this->getSubject())
            ));
        }

        /** @var Admin $childItem */
        foreach ($this->getChildren() as $childAdmin) {
            if ($childAdmin instanceof QuestionOptionAdmin) {
                if (in_array($this->getSubject()->getFormType(), FormType::getOptionBasedTypes())) {
                    $menu->addChild('Choices', array(
                        'uri' => $childAdmin->generateUrl('list'),
                        'current' => $childAdmin === $activeChildAdmin,
                    ));
                }
            } elseif ($childAdmin instanceof QuestionActionAdmin) {
                $menu->addChild('Answer Actions', array(
                    'uri' => $childAdmin->generateUrl('list'),
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
}
