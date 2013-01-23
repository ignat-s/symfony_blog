<?php

namespace Acme\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'author',
            'text',
            array('label' => 'form.comment.author')
        );
        $builder->add(
            'email',
            'email',
            array('label' => 'form.comment.email')
        );
        $builder->add(
            'body',
            'textarea',
            array('label' => 'form.comment.body')
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Acme\BlogBundle\Model\Comment',
                'label' => 'Add Comment',
                'translation_domain' => 'AcmeBlogBundle'
            )
        );
    }

    public function getName()
    {
        return 'comment';
    }
}
