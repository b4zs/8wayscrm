<?php

namespace Application\MediaBundle\Admin;

use Application\CrmBundle\Enum\FileCategoryEnum;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GalleryHasMediaAdmin extends \Sonata\MediaBundle\Admin\GalleryHasMediaAdmin
{
	/**
	 * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
	 */
	protected function configureFormFields(FormMapper $formMapper)
	{
		parent::configureFormFields($formMapper);

		$formMapper->add('fileCategory', ChoiceType::class, [
            'choices' => FileCategoryEnum::getChoices(),
        ]);

		$formMapper->remove('enabled');
	}


}