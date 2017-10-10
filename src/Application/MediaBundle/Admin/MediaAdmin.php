<?php


namespace Application\MediaBundle\Admin;

use Application\CrmBundle\Enum\FileCategoryEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin as BaseAdmin;

class MediaAdmin extends BaseAdmin
{
    /**
     * @param DatagridMapper $dataGridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $dataGridMapper)
    {
        $dataGridMapper->add('fileCategory', 'doctrine_orm_string', [
            'show_filter' => true,
        ], 'choice', array('choices' => FileCategoryEnum::getChoices())
        );
        parent::configureDatagridFilters($dataGridMapper);

    }
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);

        $this->setListMode('list');

        $listMapper->add('fileCategory');
    }

}