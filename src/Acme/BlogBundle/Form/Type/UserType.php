<?php

namespace Acme\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'username',
            'text',
            array('label' => 'form.username')
        );
        $builder->add(
            'email',
            'email',
            array('label' => 'form.email')
        );
        $builder->add(
            'plainPassword',
            'repeated',
            array(
                'first_name' => 'password',
                'second_name' => 'confirm',
                'type' => 'password',
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'form.password.mismatch',
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Acme\BlogBundle\Document\User',
                'translation_domain' => 'AcmeBlogBundle',
            )
        );
    }

    public function getName()
    {
        return 'user';
    }
}
