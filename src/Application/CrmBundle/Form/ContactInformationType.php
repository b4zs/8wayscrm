<?php

namespace Application\CrmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactInformationType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm($builder, array $options)
    {
        $builder
            ->add('companyPhone', null, array(
                'required' => false,
            ))
            ->add('privatePhone', null, array(
                'required' => false,
            ))
            ->add('companyEmail', 'email', array(
                'required' => false,
            ))
            ->add('privateEmail', 'email', array(
                'required' => false,
            ))
            ->add('skypeId', null, array(
                'required' => false,
            ))
            ->add('facebookId', null, array(
                'required' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\CrmBundle\Entity\ContactInformation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_crmbundle_contactinformation';
    }
}
