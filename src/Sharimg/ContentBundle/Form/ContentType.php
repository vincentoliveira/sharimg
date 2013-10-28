<?php

namespace Sharimg\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
                'label' => 'sharimg.date',
                'data' => new \DateTime()
            ))
            ->add('description', 'text', array(
                'label' => 'sharimg.description',
            ))
            ->add('file', 'file', array(
                'label' => 'sharimg.file',
                'required' => false,
                'attr' => array(
                ),
            ))
            ->add('url', 'text', array(
                'label' => 'sharimg.url',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control',
                ),
            ))
            ->add('visible', 'checkbox', array(
                'label' => 'sharimg.is_visible',
                'required' => false,
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'joke';
    }
}
