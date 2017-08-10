<?php

namespace Application\QuotationGeneratorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FillOutAnswerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('step')
//            ->add('data')
            ->add('id', 'entity', array(
                'property_path' => 'question',
                'class' => 'Application\QuotationGeneratorBundle\Entity\Question',
            ))
            ->add('value')
//            ->add('option')
//            ->add('fillOut')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Application\QuotationGeneratorBundle\Entity\FillOutAnswer',
            'csrf_protection'   => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_quotationgeneratorbundle_filloutanswer';
    }
}
