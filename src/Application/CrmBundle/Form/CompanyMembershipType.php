<?php

namespace Application\CrmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyMembershipType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm($builder, array $options)
    {
        $builder
            ->add('startDate', 'date', array(
                'widget'    => 'single_text',
                'required'  => false,
            ))
            ->add('endDate', 'date', array(
                'widget'    => 'single_text',
                'required'  => false,
            ))
            ->add('workPermit', null, array(
                'required' => false,
            ))
            ->add('holidaysRemaining', null, array(
                'required' => false,
            ))
//            ->add('company')
//            ->add('person')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\CrmBundle\Entity\CompanyMembership'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'application_crmbundle_companymembership';
    }
}
