<?php

namespace Acme\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserType());
        $builder->add(
            'terms',
            'checkbox',
            array(
                'property_path' => 'termsAccepted',
                'label' => 'form.terms',
                'translation_domain' => 'AcmeBlogBundle'
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'validation_groups' => array('Default', 'registration'),
            )
        );
    }

    public function getName()
    {
        return 'registration';
    }
}
