<?php


namespace Application\AdminBundle\Form\Type;


use Symfony\Component\Form\AbstractType;

class DeleteCheckboxType extends AbstractType
{

	public function getName()
	{
		return 'gb_delete_checkbox';
	}

	public function getParent()
	{
		return 'checkbox';
	}
}