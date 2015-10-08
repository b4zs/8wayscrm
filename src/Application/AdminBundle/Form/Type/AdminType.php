<?php


namespace Application\AdminBundle\Form\Type;


use Sonata\AdminBundle\Form\DataTransformer\ArrayToModelTransformer;
use Sonata\AdminBundle\Form\Type\AdminType as BaseAdminType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminType extends BaseAdminType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		parent::buildForm($builder, $options);

		$admin = $this->getAdmin($options);

		if ($builder->has('_delete')) {
			$builder->remove('_delete');
			$builder->add('_delete', 'gb_delete_checkbox', array('required' => false, 'mapped' => false, 'translation_domain' => $admin->getTranslationDomain()));
//			$builder->add('_open', 'gb_open_button', array('required' => false, 'mapped' => false, 'translation_domain' => $admin->getTranslationDomain()));
		}
	}
}