<?php
namespace Application\CrmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'required' => true,
            'constraints' => array(
                new NotBlank()
            )
        ));
        $builder->add('value', 'text');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Application\CrmBundle\Entity\CustomProperty'
        ));
    }

    public function getName()
    {
        return parent::getName(); // TODO: Change the autogenerated stub
    }


}