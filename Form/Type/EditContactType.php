<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Contact editing form type class.
 */
class EditContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Contact name'),
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Email address(es)'),
                'attr' => [
                    'maxlength' => 200
                ],
                'help' => $translator->__('You may enter a single address or a comma separated list of addresses.')
            ])
            ->add('public', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Public'),
                'required' => false
            ])
            ->add('senderName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Sender name'),
                'required' => false,
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('senderEmail', 'Symfony\Component\Form\Extension\Core\Type\EmailType', [
                'label' => $translator->__('Sender email address'),
                'required' => false,
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('sendingSubject', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Subject'),
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ])
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => $translator->__('Save'),
                'icon' => 'fa-check',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => $translator->__('Cancel'),
                'icon' => 'fa-times',
                'attr' => [
                    'class' => 'btn btn-default'
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zikulaformiculamodule_editcontact';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Zikula\FormiculaModule\Entity\ContactEntity',
            'translator' => null
        ]);
    }
}
