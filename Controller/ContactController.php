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

namespace Zikula\FormiculaModule\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Zikula\ExtensionsModule\AbstractExtension;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\FormiculaModule\Entity\ContactEntity;
use Zikula\FormiculaModule\Entity\Repository\ContactRepository;
use Zikula\FormiculaModule\Form\Type\DeleteContactType;
use Zikula\FormiculaModule\Form\Type\EditContactType;
use Zikula\FormiculaModule\Helper\EnvironmentHelper;
use Zikula\PermissionsModule\Annotation\PermissionCheck;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @var EnvironmentHelper
     */
    private $environmentHelper;

    public function __construct(
        AbstractExtension $extension,
        PermissionApiInterface $permissionApi,
        VariableApiInterface $variableApi,
        TranslatorInterface $translator,
        ContactRepository $contactRepository,
        EnvironmentHelper $environmentHelper
    ) {
        parent::__construct($extension, $permissionApi, $variableApi, $translator);
        $this->contactRepository = $contactRepository;
        $this->environmentHelper = $environmentHelper;
    }

    /**
     * Show a list of contacts.
     *
     * @Route("/view")
     * @PermissionCheck("admin")
     * @Template("@ZikulaFormiculaModule/Contact/view.html.twig")
     * @Theme("admin")
     */
    public function viewAction(): array
    {
        // check necessary environment
        $this->environmentHelper->check();

        $allContacts = $this->contactRepository->findBy([], ['cid' => 'ASC']);

        // only use those contacts where we have the necessary rights for
        $visibleContacts = [];
        foreach ($allContacts as $contact) {
            $contactId = $contact->getCid();
            if (!$this->hasPermission('ZikulaFormiculaModule::', ":${contactId}:", ACCESS_EDIT)) {
                continue;
            }

            $contactArray = $contact->toArray();
            $contactArray['allowEdit'] = true;
            $contactArray['allowDelete'] = $this->hasPermission('ZikulaFormiculaModule::', ":${contactId}:", ACCESS_DELETE);
            $visibleContacts[] = $contactArray;
        }

        return [
            'contacts' => $visibleContacts
        ];
    }

    /**
     * Allows adding and editing contacts.
     *
     * @Route("/edit")
     * @PermissionCheck("admin")
     * @Template("@ZikulaFormiculaModule/Contact/edit.html.twig")
     * @Theme("admin")
     */
    public function editAction(Request $request)
    {
        // check necessary environment
        $this->environmentHelper->check();

        $entityManager = $this->getDoctrine()->getManager();
        $contactId = $request->query->getInt('cid', 0);

        if ($contactId < 1) {
            $mode = 'create';
            $contact = new ContactEntity();
            $contact->setPublic(true);
        } else {
            $mode = 'edit';
            $contact = $this->contactRepository->find($contactId);
            if (false === $contact) {
                $this->addFlash('error', 'Contact could not be found.');

                return $this->redirectToRoute('zikulaformiculamodule_contact_view');
            }
        }

        $form = $this->createForm(EditContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $valid = true;
            if ($form->get('save')->isClicked()) {
                $contact = $form->getData();

                // email addresses can be a comma separated string, split and check separately
                $mailAddress = $contact->getEmail();
                $mailAddress = preg_replace('/\s*/m', '', $mailAddress); // remove spaces
                $addresses = explode(',', $mailAddress);
                foreach ($addresses as $address) {
                    if (false === filter_var($address, FILTER_VALIDATE_EMAIL)) {
                        $this->addFlash('error', $this->trans('Error! Incorrect email address [%s%] supplied.', ['%s%' => $address]));
                        $valid = false;
                        break;
                    }
                }
                if ($valid) {
                    $contact->setEmail($mailAddress);

                    $entityManager->persist($contact);
                    $entityManager->flush();
                    if ('create' === $mode) {
                        $this->addFlash('status', 'Done! Contact created.');
                    } else {
                        $this->addFlash('status', 'Done! Contact updated.');
                    }
                }
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', 'Operation cancelled.');
            }

            if ($valid) {
                return $this->redirectToRoute('zikulaformiculamodule_contact_view');
            }
        }

        return [
            'form' => $form->createView(),
            'mode' => $mode,
            'contact' => $contact
        ];
    }

    /**
     * Deletes an existing contact from the database.
     *
     * @Route("/delete")
     * @PermissionCheck("delete")
     * @Template("@ZikulaFormiculaModule/Contact/delete.html.twig")
     * @Theme("admin")
     */
    public function deleteAction(Request $request)
    {
        // check necessary environment
        $this->environmentHelper->check();

        $entityManager = $this->getDoctrine()->getManager();
        $contactId = $request->query->getInt('cid', 0);

        $contact = $this->contactRepository->find($contactId);
        if (false === $contact) {
            $this->addFlash('error', 'Contact could not be found.');

            return $this->redirectToRoute('zikulaformiculamodule_contact_view');
        }

        $form = $this->createForm(DeleteContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $contact = $form->getData();
                $entityManager->remove($contact);
                $entityManager->flush();
                $this->addFlash('status', 'Done! Contact deleted.');
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', 'Operation cancelled.');
            }

            return $this->redirectToRoute('zikulaformiculamodule_contact_view');
        }

        return [
            'form' => $form->createView(),
            'contact' => $contact
        ];
    }
}
