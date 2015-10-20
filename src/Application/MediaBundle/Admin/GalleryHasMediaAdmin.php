<?php

namespace Application\MediaBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;

class GalleryHasMediaAdmin extends \Sonata\MediaBundle\Admin\GalleryHasMediaAdmin
{
	/**
	 * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		parent::configureFormFields($formMapper);

		$formMapper->remove('enabled');
	}


}