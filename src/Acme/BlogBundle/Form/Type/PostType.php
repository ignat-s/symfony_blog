<?php

namespace Acme\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            'text',
            array('label' => 'form.post.title')
        );
        $builder->add(
            'body',
            'textarea',
            array('label' => 'form.post.body')
        );
        $builder->add(
            'publicationDate',
            'datetime',
            array('label' => 'form.post.publicationDate')
        );
        $builder->add(
            'tagsString',
            'text',
            array('label' => 'form.post.tags')
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Acme\BlogBundle\Model\Post',
                'translation_domain' => 'AcmeBlogBundle',
            )
        );
    }

    public function getName()
    {
        return 'post';
    }
}
