<?php


namespace Application\QuotationGeneratorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpenButtonType extends AbstractType
{

	public function getName()
	{
		return 'gb_open_button';
	}

	public function getParent()
	{
		return 'text';
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'virtual'       => true,
			'inherit_data'  => true,
			'required'      => false,
			'label'         => 'Open',
			'attr'          => array(
				'class' => 'btn btn-info',
			),
		));
	}
}