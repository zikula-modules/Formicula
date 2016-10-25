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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
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
     * @Template
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
        $this->get('zikula_formicula_module.helper.environment_checker')->check();

        // read all items
        $allContacts = ModUtil::apiFunc('Formicula', 'admin', 'readContacts');
        // only use those where we have the necessary rights for
        $visibleContacts = [];
        foreach ($allContacts as $contact) {
            $contactId = $contact->getCid();
            if (!$this->hasPermission('ZikulaFormiculaModule::', ":$contactId:", ACCESS_EDIT)) {
                continue;
            }

            $visibleContacts[] = [
                'cid'        => $contactId,
                'name'       => $contact->getName(),
                'email'      => $contact->getEmail(),
                'public'     => $contact->isPublic(),
                'sname'      => $contact->getSenderName(),
                'semail'     => $contact->getSenderEmail(),
                'ssubject'   => $contact->getSendingSubject(),
                'acc_edit'   => true,
                'acc_delete' => $this->hasPermission('ZikulaFormiculaModule::', ":$contactId:", ACCESS_DELETE)
            ];
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
     * @Template
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function editAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADD)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $this->get('zikula_formicula_module.helper.environment_checker')->check();

        $legacyHandler = new Formicula_Form_Handler_Admin_Edit();

        return [];
    }

    /**
     * Deletes an existing contact from the database.
     *
     * @Route("/delete")
     * @Theme("admin")
     * @Template
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
        $this->get('zikula_formicula_module.helper.environment_checker')->check();

        $contactId = $request->query->getDigits('cid', -1);
        $confirmation = $request->request->get('confirmation', '');

        $contact = ModUtil::apiFunc('Formicula', 'admin', 'getContact', ['cid' => $contactId]);
        if (false === $contact) {
            $this->addFlash('error', $this->__('Unknown contact'));

            return $this->redirectToRoute('zikulaformiculamodule_contact_view');
        }

        // Check for confirmation.
        if (!empty($confirmation)) {
            // Confirm security token code
            $this->checkCsrfToken();        

            if (ModUtil::apiFunc('Formicula', 'admin', 'deleteContact', ['cid' => $contactId])) {
                // Success
                $this->addFlash('status', $this->__('Contact has been deleted'));
            }

            return $this->redirectToRoute('zikulaformiculamodule_contact_view');
        }

        return [
            'contact' => $contact
        ];
    }
}
