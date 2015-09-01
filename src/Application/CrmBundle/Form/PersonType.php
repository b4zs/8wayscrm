<?php

namespace Application\CrmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm($builder, array $options)
    {
        $builder
            ->add('personalData')
            ->add('contactInformation')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\CrmBundle\Entity\Person'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_crmbundle_person';
    }
}
