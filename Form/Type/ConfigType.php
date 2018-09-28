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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Configuration form type class.
 */
class ConfigType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('defaultForm', ChoiceType::class, [
                'label' => $translator->__('Default form'),
                'choices' => $options['formChoices'],
                'expanded' => false,
                'multiple' => false,
                'help' => $translator->__('This form is used when no form is specified.')
            ])
            ->add('showCompany', CheckboxType::class, [
                'label' => $translator->__('Show company'),
                'required' => false
            ])
            ->add('showPhone', CheckboxType::class, [
                'label' => $translator->__('Show phone number'),
                'required' => false
            ])
            ->add('showUrl', CheckboxType::class, [
                'label' => $translator->__('Show url'),
                'required' => false
            ])
            ->add('showLocation', CheckboxType::class, [
                'label' => $translator->__('Show location'),
                'required' => false
            ])
            ->add('showComment', CheckboxType::class, [
                'label' => $translator->__('Show comments textarea'),
                'required' => false
            ])
            ->add('showFileAttachment', CheckboxType::class, [
                'label' => $translator->__('Show file attachment'),
                'required' => false
            ])
            ->add('uploadDirectory', TextType::class, [
                'label' => $translator->__('Directory for uploaded files'),
                'attr' => [
                    'maxlength' => 150
                ]
            ])
            ->add('deleteUploadedFiles', CheckboxType::class, [
                'label' => $translator->__('Delete uploaded file(s) after sending'),
                'required' => false
            ])
            ->add('sendConfirmationToUser', CheckboxType::class, [
                'label' => $translator->__('Send confirmation email to user'),
                'required' => false
            ])
            ->add('defaultAdminFormat', ChoiceType::class, [
                'label' => $translator->__('Default email format for admin emails'),
                'choices' => [
                    $translator->__('HTML') => 'html',
                    $translator->__('Plain text') => 'plain'
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('defaultUserFormat', ChoiceType::class, [
                'label' => $translator->__('Default email format for user emails'),
                'choices' => [
                    $translator->__('HTML') => 'html',
                    $translator->__('Plain text') => 'plain',
                    $translator->__('None') => 'none'
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('showUserFormat', CheckboxType::class, [
                'label' => $translator->__('Show user email format selector'),
                'required' => false
            ])
            ->add('useContactsAsSender', CheckboxType::class, [
                'label' => $translator->__('Use contact mail addresses as sender'),
                'required' => false,
                'help' => $translator->__('Disable this if you experience problems with your SMTP server')
            ])
            ->add('enableSpamCheck', CheckboxType::class, [
                'label' => $translator->__('Activate simple spam check'),
                'required' => false,
                'alert' => [$translator->__('Make sure you the necessary form fields are available, see the docs for more information. This option will be turned off by Formicula automatically if no PHP functions for creating images are available.') => 'info']
            ])
            ->add('excludeSpamCheck', TextType::class, [
                'label' => $translator->__('Do not use spam check in these forms'),
                'required' => false,
                'attr' => [
                    'maxlength' => 40
                ],
                'help' => $translator->__('Enter comma separated list of form ids or leave empty for using the spam check in all forms.')
            ])
            ->add('storeSubmissionData', CheckboxType::class, [
                'label' => $translator->__('Store submitted data in database'),
                'required' => false
            ])
            ->add('storeSubmissionDataForms', TextType::class, [
                'label' => $translator->__('Only store submissions from these forms'),
                'required' => false,
                'attr' => [
                    'maxlength' => 40
                ],
                'help' => $translator->__('Enter comma separated list of form ids or leave empty for storing all forms.')
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
        return 'zikulaformiculamodule_config';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translator' => null,
            'formChoices' => []
        ]);
    }
}
