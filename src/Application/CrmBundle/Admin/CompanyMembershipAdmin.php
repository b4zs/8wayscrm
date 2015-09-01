<?php

namespace Application\CrmBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CompanyMembershipAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('person')
            ->add('company')
            ->add('startDate')
            ->add('endDate')
            ->add('workPermit')
            ->add('holidaysRemaining')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
	        ->add('person')
	        ->add('company')
            ->add('startDate')
            ->add('endDate')
            ->add('workPermit')
            ->add('holidaysRemaining')
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
	    $parentAdmin = $this->getParentAdmin($formMapper);

	    if ($parentAdmin !== 'person') {
		    $formMapper->add('person', 'sonata_type_model_list', array(
			    'required' => true,
			    'btn_delete' => false,
		    ));
	    }

	    $formMapper->add('role', 'text', array(
		    'required' => false,
	    ));

	    if ($parentAdmin !== 'company') {
		    $formMapper->add('company', 'sonata_type_model_list', array(
				'required' => true,
		    ));
	    }

	    if (!$parentAdmin) {
	        $formMapper
	            ->add('startDate', 'date', array(
	                'required' => false,
	                'widget'    => 'single_text',
	            ))
	            ->add('endDate', 'date', array(
	                'required' => false,
	                'widget'    => 'single_text',
	            ))
	        ;
		    $formMapper
	            ->add('workPermit', null, array(
	                'required' => false,
	            ))
	            ->add('holidaysRemaining', null, array(
	                'required' => false,
	            ))
	        ;
	    }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('startDate')
            ->add('endDate')
            ->add('workPermit')
            ->add('holidaysRemaining')
        ;
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
}
