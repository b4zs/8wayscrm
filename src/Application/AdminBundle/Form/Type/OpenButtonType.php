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
				'class'     => 'btn btn-success btn-sm',
				'target'    => '_blank',
				'title'     => 'Open detail in new tab',
			),
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$class = $form->getData()
			? ClassUtils::getClass($form->getData())
			: null;

		if ($class) {
			$admin = $this->pool->getAdminByClass($class);
			$object = $form->getData();
			if ($object->getId() && $admin->isGranted('EDIT', $object)) {
				$view->vars['url'] = $admin->generateObjectUrl('edit', $object);
			}
		}

		if (empty($view->vars['url'])) {
			$view->vars['attr']['class'].=' disabled';
		}
	}

	/**
	 * @param Pool $pool
	 */
	public function setPool($pool)
	{
		$this->pool = $pool;
	}


}