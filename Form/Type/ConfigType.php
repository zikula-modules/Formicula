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
            ->add('defaultForm', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $translator->__('Default form'),
                'choices' => $options['formChoices'],
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false,
                'help' => $translator->__('This form is used when no form is specified.')
            ])
            ->add('showCompany', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Show company'),
                'required' => false
            ])
            ->add('showPhone', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Show phone number'),
                'required' => false
            ])
            ->add('showUrl', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Show url'),
                'required' => false
            ])
            ->add('showLocation', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Show location'),
                'required' => false
            ])
            ->add('showComment', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Show comments textarea'),
                'required' => false
            ])
            ->add('showFileAttachment', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Show file attachment'),
                'required' => false
            ])
            ->add('uploadDirectory', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Directory for uploaded files'),
                'max_length' => 150
            ])
            ->add('deleteUploadedFiles', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Delete uploaded file(s) after sending'),
                'required' => false
            ])
            ->add('sendConfirmationToUser', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Send confirmation email to user'),
                'required' => false
            ])
            ->add('defaultAdminFormat', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $translator->__('Default email format for admin emails'),
                'choices' => [
                    $translator->__('HTML') => 'html',
                    $translator->__('Plain text') => 'plain'
                ],
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false
            ])
            ->add('defaultUserFormat', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $translator->__('Default email format for user emails'),
                'choices' => [
                    $translator->__('HTML') => 'html',
                    $translator->__('Plain text') => 'plain',
                    $translator->__('None') => 'none'
                ],
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false
            ])
            ->add('showUserFormat', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Show user email format selector'),
                'required' => false
            ])
            ->add('useContactsAsSender', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Use contact mail addresses as sender'),
                'required' => false,
                'help' => $translator->__('Disable this if you experience problems with your SMTP server')
            ])
            ->add('enableSpamCheck', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Activate simple spam check'),
                'required' => false,
                'alert' => [$translator->__('Make sure you the necessary form fields are available, see the docs for more information. This option will be turned off by Formicula automatically if no PHP functions for creating images are available.') => 'info']
            ])
            ->add('excludeSpamCheck', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Do not use spam check in these forms'),
                'required' => false,
                'max_length' => 40,
                'help' => $translator->__('Enter comma separated list of form ids or leave empty for using the spam check in all forms.')
            ])
            ->add('storeSubmissionData', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $translator->__('Store submitted data in database'),
                'required' => false
            ])
            ->add('storeSubmissionDataForms', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $translator->__('Only store submissions from these forms'),
                'required' => false,
                'max_length' => 40,
                'help' => $translator->__('Enter comma separated list of form ids or leave empty for storing all forms.')
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
        return 'zikulaformiculamodule_config';
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
            'formChoices' => []
        ]);
    }
}
