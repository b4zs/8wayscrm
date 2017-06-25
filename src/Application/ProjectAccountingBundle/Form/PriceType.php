<?php


namespace Application\ProjectAccountingBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('amount', 'number', array(
            'required' => false,
            'precision' => 2,
        ));
        $builder->add('currency', 'choice', array(
            'required' => false,
            'choices' => array(
                'HUF' => 'HUF',
                'EUR' => 'EUR',
            ),
        ));
    }

    public function getName()
    {
        return 'accounting_price';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\ProjectAccountingBundle\Entity\Price',
        ));
    }


}