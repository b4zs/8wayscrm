<?php

namespace Application\CrmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonalDataType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm($builder, array $options)
    {
        $propertyPath = isset($options['property_path']) ? $options['property_path'].'.' : '';

            /**/
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\CrmBundle\Entity\PersonalData'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_crmbundle_personaldata';
    }
}
