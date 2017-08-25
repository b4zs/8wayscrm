<?php


namespace Application\ClassificationBundle\Form\Type;


use Application\ClassificationBundle\Form\DataTransformer\TagsDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TagsType extends AbstractType
{
	/**
	 * @var TagsDataTransformer
	 */
	private $tagsDataTransformer;

	public function __construct(TagsDataTransformer $tagsDataTransformer)
	{
		$this->tagsDataTransformer = $tagsDataTransformer;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer($this->tagsDataTransformer);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'class'         => 'Core\ClassificationBundle\Entity\Tag',
			'empty_value'   => 'label.tags',
			'required'      => false,
			'multiple'      => true,
			'property'      => 'name',
			'allow_add'     => true,
		));
	}

	public function getParent()
	{
		return 'text';
	}

	public function getName()
	{
		return 'app_classification_tags';
	}

	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['allow_add'] = $options['allow_add'];
	}
} 