<?php

namespace Application\CrmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm($builder, array $options)
    {
        $builder
            ->add('country')
            ->add('state')
            ->add('city')
            ->add('street')
            ->add('streetNumber')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\CrmBundle\Entity\Address'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_crmbundle_address';
    }
}
