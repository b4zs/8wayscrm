<?php

namespace Application\CrmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OfficeType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm($builder, array $options)
    {
        $builder
            ->add('company')
            ->add('address')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\CrmBundle\Entity\Office'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_crmbundle_office';
    }
}
