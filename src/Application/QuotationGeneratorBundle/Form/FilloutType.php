<?php


namespace Application\QuotationGeneratorBundle\Form;


use Application\QuotationGeneratorBundle\Enum\Stage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilloutType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
//		$builder->add('stage', 'choice', array(
//			'choices' => Stage::getApiChoices(),
//		));
//
//		$builder->get('stage')->addModelTransformer(new CallbackTransformer(
//			array('Application\\QuotationGeneratorBundle\\Enum\\Stage', 'mapIntegerToApiChoice'),
//			array('Application\\QuotationGeneratorBundle\\Enum\\Stage', 'mapApiChoiceToInteger')
//		));
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'        => 'Application\\QuotationGeneratorBundle\\Entity\\FillOut',
			'csrf_protection'   => false,
		));
	}


	public function getName()
	{
		return 'fillout';
	}
}