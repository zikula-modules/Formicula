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
 * User submission form type class.
 */
class UserSubmissionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];
        $modVars = $options['modVars'];

        $builder
            ->add('form', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [])
            ->add('adminFormat', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [])
        ;
        if ($modVars['sendConfirmationToUser'] && !$modVars['showUserFormat']) {
            $builder->add('userFormat', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', []);
        }
        $builder
            ->add('cid', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $translator->__('Contact'),
                'choices' => $options['contactChoices'],
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false
            ])
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Your name'),
                'attr' => [
                    'maxlength' => 150
                ]
            ])
        ;
        if ($modVars['showCompany']) {
            $builder->add('company', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Company'),
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ]);
        }
        $builder->add('emailAddress', 'Symfony\Component\Form\Extension\Core\Type\EmailType', [
            'label' => $translator->__('Email address'),
            'attr' => [
                'maxlength' => 150,
                'placeholder' => $translator->__('Enter a valid email address')
            ]
        ]);
        if ($modVars['showPhone']) {
            $builder->add('phone', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Phone number'),
                'required' => false,
                'attr' => [
                    'maxlength' => 50
                ]
            ]);
        }
        if ($modVars['showUrl']) {
            $builder->add('url', 'Symfony\Component\Form\Extension\Core\Type\UrlType', [
                'label' => $translator->__('Website'),
                'attr' => [
                    'maxlength' => 150,
                    'placeholder' => $translator->__('Enter a valid url')
                ],
                'required' => false
            ]);
        }
        if ($modVars['showLocation']) {
            $builder->add('location', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Location'),
                'required' => false,
                'attr' => [
                    'maxlength' => 150
                ]
            ]);
        }
        if ($modVars['sendConfirmationToUser'] && $modVars['showUserFormat']) {
            $builder->add('userFormat', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $translator->__('Email confirmation format'),
                'choices' => [
                    $translator->__('HTML') => 'html',
                    $translator->__('Text') => 'plain',
                    $translator->__('None') => 'none'
                ],
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false
            ]);
        }
        if ($modVars['showComment']) {
            $builder->add('comment', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
                'label' => $translator->__('Comment'),
                'attr' => [
                    'placeholder' => $translator->__('Enter your comments here')
                ]
            ]);
        }
        if ($modVars['showFileAttachment']) {
            $builder->add('fileUpload', 'Symfony\Component\Form\Extension\Core\Type\FileType', [
                'label' => $translator->__('Attach a file'),
                'required' => false
            ]);
        }
        $builder->add('submit', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
            'label' => $translator->__('Send'),
            'icon' => 'fa-check',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zikulaformiculamodule_usersubmission';
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
            'translator' => null,
            'modVars' => [],
            'contactChoices' => []
        ]);
    }
}
