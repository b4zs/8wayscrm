<?php


namespace Application\QuotationGeneratorBundle\Form;


use Application\QuotationGeneratorBundle\Form\DataTransformer\YamlToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlArrayType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer(new YamlToArrayTransformer());
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		parent::setDefaultOptions($resolver);

		$resolver->setDefaults(array(
			'constraints' => array(
				new Callback(array('callback' => array($this, 'validateYaml'))),
			),
		));
	}


	public function getParent()
	{
		return 'textarea';
	}


	public function getName()
	{
		return 'yaml_array';
	}

	public function validateYaml($value, ExecutionContext $executionContext)
	{
		if (is_string($value)) {
			try {
				Yaml::parse($value);
			} catch (ParseException $e) {
				$executionContext->addViolation($e->getMessage());
			}
		}
	}


} 