<?php


namespace Application\AdminBundle\Form\Type;


use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OpenButtonType extends AbstractType
{
	/** @var  Pool */
	private $pool;

	public function getName()
	{
		return 'gb_open_button';
	}

	public function getParent()
	{
		return 'text';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
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

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['admin_pool'] = $this->pool;
		$view->vars['subject_class'] = $form->getData()
			? ClassUtils::getClass($form->getData())
			: null;
	}

	/**
	 * @param Pool $pool
	 */
	public function setPool($pool)
	{
		$this->pool = $pool;
	}


}