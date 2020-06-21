<?php

declare(strict_types=1);

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
use Zikula\FormiculaModule\Entity\ContactEntity;

/**
 * Contact editing form type class.
 */
class EditContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Contact name',
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email address(es)',
                'attr' => [
                    'maxlength' => 200
                ],
                'help' => 'You may enter a single address or a comma separated list of addresses.'
            ])
            ->add('public', CheckboxType::class, [
                'label' => 'Public',
                'required' => false
            ])
            ->add('senderName', TextType::class, [
                'label' => 'Sender name',
                'required' => false,
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('senderEmail', EmailType::class, [
                'label' => 'Sender email address',
                'required' => false,
                'attr' => [
                    'maxlength' => 100
                ]
            ])
            ->add('sendingSubject', TextType::class, [
                'label' => 'Subject',
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'icon' => 'fa-check',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('cancel', SubmitType::class, [
                'label' => 'Cancel',
                'icon' => 'fa-times',
                'attr' => [
                    'class' => 'btn btn-default'
                ]
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulaformiculamodule_editcontact';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactEntity::class,
        ]);
    }
}
