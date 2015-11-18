<?php


namespace Application\CrmBundle\Admin;


use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SupplierAdmin extends ClientAdmin
{
	protected function configureFormFields(FormMapper $formMapper)
	{
		parent::configureFormFields($formMapper);
		$formMapper->remove('status');
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		parent::configureListFields($listMapper);
		$listMapper->remove('status');
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		parent::configureDatagridFilters($datagridMapper);
		$datagridMapper->remove('status');
	}


}