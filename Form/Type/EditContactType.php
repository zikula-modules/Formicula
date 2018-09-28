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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('name', TextType::class, [
                'label' => $translator->__('Contact name'),
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('email', TextType::class, [
                'label' => $translator->__('Email address(es)'),
                'attr' => [
                    'maxlength' => 200
                ],
                'help' => $translator->__('You may enter a single address or a comma separated list of addresses.')
            ])
            ->add('public', CheckboxType::class, [
                'label' => $translator->__('Public'),
                'required' => false
            ])
            ->add('senderName', TextType::class, [
                'label' => $translator->__('Sender name'),
                'required' => false,
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('senderEmail', EmailType::class, [
                'label' => $translator->__('Sender email address'),
                'required' => false,
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('sendingSubject', TextType::class, [
                'label' => $translator->__('Subject'),
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => $translator->__('Save'),
                'icon' => 'fa-check',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('cancel', SubmitType::class, [
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Zikula\FormiculaModule\Entity\ContactEntity',
            'translator' => null
        ]);
    }
}
