<?php

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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\FormiculaModule\Entity\ContactEntity;
use Zikula\FormiculaModule\Form\Type\DeleteContactType;
use Zikula\FormiculaModule\Form\Type\EditContactType;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Class ContactController
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
    /**
     * Show a list of contacts.
     *
     * @Route("/view")
     * @Theme("admin")
     * @Template("ZikulaFormiculaModule:Contact:view.html.twig")
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function viewAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $this->get('zikula_formicula_module.helper.environment_helper')->check();

        $allContacts = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->findBy([], ['cid' => 'ASC']);

        // only use those contacts where we have the necessary rights for
        $visibleContacts = [];
        foreach ($allContacts as $contact) {
            $contactId = $contact->getCid();
            if (!$this->hasPermission('ZikulaFormiculaModule::', ":$contactId:", ACCESS_EDIT)) {
                continue;
            }

            $contactArray = $contact->toArray();
            $contactArray['allowEdit'] = true;
            $contactArray['allowDelete'] = $this->hasPermission('ZikulaFormiculaModule::', ":$contactId:", ACCESS_DELETE);
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
     * @Theme("admin")
     * @Template("ZikulaFormiculaModule:Contact:edit.html.twig")
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function editAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $this->get('zikula_formicula_module.helper.environment_helper')->check();

        $entityManager = $this->get('doctrine')->getManager();
        $contactId = $request->query->getDigits('cid', 0);

        if ($contactId < 1) {
            $mode = 'create';
            $contact = new ContactEntity();
            $contact->setPublic(true);
        } else {
            $mode = 'edit';
            $contact = $entityManager->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->find($contactId);
            if (false === $contact) {
                $this->addFlash('error', $this->__('Contact could not be found.'));

                return $this->redirectToRoute('zikulaformiculamodule_contact_view');
            }
        }

        $form = $this->createForm(EditContactType::class, $contact, [
            'translator' => $this->get('translator.default')
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $valid = true;
            if ($form->get('save')->isClicked()) {
                $contact = $form->getData();

                // email addresses can be a comma seperated string, split and check separately
                $mailAddress = $contact->getEmail();
                $mailAddress = preg_replace('/\s*/m', '', $mailAddress); // remove spaces
                $addresses = explode(',', $mailAddress);
                foreach ($addresses as $address) {
                    if (false === filter_var($address, FILTER_VALIDATE_EMAIL)) {
                        $this->addFlash('error', $this->__f('Error! Incorrect email address [%s] supplied.', ['%s' => $address]));
                        $valid = false;
                        break;
                    }
                }
                if ($valid) {
                    $contact->setEmail($mailAddress);

                    $entityManager->persist($contact);
                    $entityManager->flush();
                    if ($mode == 'create') {
                        $this->addFlash('status', $this->__('Done! Contact created.'));
                    } else {
                        $this->addFlash('status', $this->__('Done! Contact updated.'));
                    }
                }
            }
            if ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->__('Operation cancelled.'));
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
     * @Theme("admin")
     * @Template("ZikulaFormiculaModule:Contact:delete.html.twig")
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_DELETE)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $this->get('zikula_formicula_module.helper.environment_helper')->check();

        $entityManager = $this->get('doctrine')->getManager();
        $contactId = $request->query->getDigits('cid', 0);

        $contact = $entityManager->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->find($contactId);
        if (false === $contact) {
            $this->addFlash('error', $this->__('Contact could not be found.'));

            return $this->redirectToRoute('zikulaformiculamodule_contact_view');
        }

        $form = $this->createForm(DeleteContactType::class, $contact, [
            'translator' => $this->get('translator.default')
        ]);

        if ($form->handleRequest($request)->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $contact = $form->getData();
                $entityManager->remove($contact);
                $entityManager->flush();
                $this->addFlash('status', $this->__('Done! Contact deleted.'));
            }
            if ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->__('Operation cancelled.'));
            }

            return $this->redirectToRoute('zikulaformiculamodule_contact_view');
        }

        return [
            'form' => $form->createView(),
            'contact' => $contact
        ];
    }
}
