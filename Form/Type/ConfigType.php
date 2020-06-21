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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('defaultForm', ChoiceType::class, [
                'label' => 'Default form',
                'choices' => $options['formChoices'],
                'expanded' => false,
                'multiple' => false,
                'help' => 'This form is used when no form is specified.'
            ])
            ->add('showCompany', CheckboxType::class, [
                'label' => 'Show company',
                'required' => false
            ])
            ->add('showPhone', CheckboxType::class, [
                'label' => 'Show phone number',
                'required' => false
            ])
            ->add('showUrl', CheckboxType::class, [
                'label' => 'Show url',
                'required' => false
            ])
            ->add('showLocation', CheckboxType::class, [
                'label' => 'Show location',
                'required' => false
            ])
            ->add('showComment', CheckboxType::class, [
                'label' => 'Show comments textarea',
                'required' => false
            ])
            ->add('showFileAttachment', CheckboxType::class, [
                'label' => 'Show file attachment',
                'required' => false
            ])
            ->add('uploadDirectory', TextType::class, [
                'label' => 'Directory for uploaded files',
                'attr' => [
                    'maxlength' => 150
                ]
            ])
            ->add('deleteUploadedFiles', CheckboxType::class, [
                'label' => 'Delete uploaded file(s) after sending',
                'required' => false
            ])
            ->add('sendConfirmationToUser', CheckboxType::class, [
                'label' => 'Send confirmation email to user',
                'required' => false
            ])
            ->add('defaultAdminFormat', ChoiceType::class, [
                'label' => 'Default email format for admin emails',
                'choices' => [
                    'HTML' => 'html',
                    'Plain text' => 'plain'
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('defaultUserFormat', ChoiceType::class, [
                'label' => 'Default email format for user emails',
                'choices' => [
                    'HTML' => 'html',
                    'Plain text' => 'plain',
                    'None' => 'none'
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('showUserFormat', CheckboxType::class, [
                'label' => 'Show user email format selector',
                'required' => false
            ])
            ->add('useContactsAsSender', CheckboxType::class, [
                'label' => 'Use contact mail addresses as sender',
                'required' => false,
                'help' => 'Disable this if you experience problems with your SMTP server'
            ])
            ->add('enableSpamCheck', CheckboxType::class, [
                'label' => 'Activate simple spam check',
                'required' => false,
                'alert' => ['Make sure you the necessary form fields are available, see the docs for more information. This option will be turned off by Formicula automatically if no PHP functions for creating images are available.' => 'info']
            ])
            ->add('excludeSpamCheck', TextType::class, [
                'label' => 'Do not use spam check in these forms',
                'required' => false,
                'attr' => [
                    'maxlength' => 40
                ],
                'help' => 'Enter comma separated list of form ids or leave empty for using the spam check in all forms.'
            ])
            ->add('storeSubmissionData', CheckboxType::class, [
                'label' => 'Store submitted data in database',
                'required' => false
            ])
            ->add('storeSubmissionDataForms', TextType::class, [
                'label' => 'Only store submissions from these forms',
                'required' => false,
                'attr' => [
                    'maxlength' => 40
                ],
                'help' => 'Enter comma separated list of form ids or leave empty for storing all forms.'
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
        return 'zikulaformiculamodule_config';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'formChoices' => []
        ]);
    }
}
