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

namespace Zikula\FormiculaModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Translation\TranslatorTrait;
use Zikula\ExtensionsModule\ModuleInterface\Content\Form\Type\AbstractContentFormType;
use Zikula\FormiculaModule\Entity\Repository\ContactRepository;

/**
 * Form content type form type.
 */
class FormType extends AbstractContentFormType
{
    use TranslatorTrait;

    /**
     * @var ContactRepository
     */
    private $contactRepository;

    public function __construct(
        TranslatorInterface $translator,
        ContactRepository $contactRepository
    ) {
        $this->setTranslator($translator);
        $this->contactRepository = $contactRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $allContacts = $this->contactRepository->findBy([], ['name' => 'ASC']);
        $contactChoices = [];
        $contactChoices[$this->trans('All public contacts or form default')] = -1;

        // only use public contacts
        foreach ($allContacts as $contact) {
            if (!$contact->isPublic()) {
                continue;
            }

            $contactChoices[$contact->getName()] = $contact->getCid();
        }

        $builder
            ->add('form', IntegerType::class, [
                'label' => $this->trans('Form #')
            ])
            ->add('contact', ChoiceType::class, [
                'label' => $this->trans('Show contact'),
                'choices' => $contactChoices
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zikulaformiculamodule_contenttype_form';
    }
}
