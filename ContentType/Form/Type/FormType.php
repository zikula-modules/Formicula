<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\ContentType\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\FormiculaModule\Entity\Repository\ContactRepository;

/**
 * Form content type form type.
 */
class FormType extends AbstractContentFormType
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $em
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityManagerInterface $em
    ) {
        $this->setTranslator($translator);
        $this->contactRepository = $em->getRepository('Zikula\FormiculaModule\Entity\ContactEntity');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $allContacts = $this->contactRepository->findBy([], ['name' => 'ASC']);
        $contactChoices = [];
        $contactChoices[$this->__('All public contacts or form default')] = -1;

        // only use public contacts
        foreach ($allContacts as $contact) {
            if (!$contact->isPublic()) {
                continue;
            }

            $contactChoices[$contact->getName()] = $contact->getCid();
        }

        $builder
            ->add('form', IntegerType::class, [
                'label' => $this->__('Form #', 'zikulaformiculamodule')
            ])
            ->add('contact', ChoiceType::class, [
                'label' => $this->__('Show contact', 'zikulaformiculamodule'),
                'choices' => $contactChoices,
                'required' => false
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
