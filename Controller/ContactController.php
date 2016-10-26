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
use Zikula\FormiculaModule\Entity\ContactEntity;
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
     * @Template
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
        $this->get('zikula_formicula_module.helper.environment_checker')->check();

        $contactId = $request->query->getDigits('cid', -1);

        if ($contactId == -1) {
            $mode = 'create';
            $contact = new ContactEntity();
            $contact->setPublic(true);
        } else {
            $mode = 'edit';
            $contact = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->find($contactId);
            if (false === $contact) {
                $this->addFlash('error', $this->__('Contact could not be found.'));

                return $this->redirectToRoute('zikulaformiculamodule_contact_view');
            }
        }
/*
        if ($args['commandName'] == 'submit') {
            $ok = $view->isValid();

            $data = $view->getValues();
            $data['cid'] = $this->cid;
            $data['public'] = (int)$data['public'];

            // copy cname to name for updating the db
            $data['name'] = $data['cname'];

            // no deletion, further checks needed
            if (empty($data['cname'])) {
                $ifield = & $view->getPluginById('cname');
                $ifield->setError(DataUtil::formatForDisplay($this->__('Error! No contact name.')));
                $ok = false;
            }
            if (empty($data['email'])) {
                $ifield = & $view->getPluginById('email');
                $ifield->setError(DataUtil::formatForDisplay($this->__('Error! No email address supplied.')));
                $ok = false;
            } else {
                // email addresses can be a comma seperated string, split and check seperately.
                $data['email'] = preg_replace('/\s*               /m', '', $data['email']); // remove spaces
                $aMail = explode(',', $data['email']);
                for ($i = 0; $i < count($aMail); $i++) {
                    if (!System::varValidate($aMail[$i], 'email')) {
                        $ifield = & $view->getPluginById('email');
                        $ifield->setError(DataUtil::formatForDisplay($this->__f('Error! Incorrect email address [%s] supplied.', $aMail[$i])));
                        $ok = false;
                        break;
                    }
                }
            }
            if (!empty($data['semail']) && !System::varValidate($data['semail'], 'email')) {
                $ifield = & $view->getPluginById('semail');
                $ifield->setError(DataUtil::formatForDisplay($this->__('Error! Incorrect email address supplied.')));
                $ok = false;
            }

            if (!$ok) {
                return false;
            }

            // The API function is called
            if ($data['cid'] == -1) {
                if (false !== ModUtil::apiFunc('Formicula', 'admin', 'createContact', $data)) {
                    // Success
                    LogUtil::registerStatus($this->__('Contact created'));
                } else {
                    LogUtil::registerError($this->__('Error creating contact!'));
                }
            } else {
                if (false !== ModUtil::apiFunc('Formicula', 'admin', 'updateContact', $data)) {
                    // Success
                    LogUtil::registerStatus($this->__('Contact info has been updated'));
                } else {
                    LogUtil::registerError($this->__('Error updating contact!'));
                }
            }
        }

        return System::redirect(ModUtil::url('Formicula', 'contact', 'view'));
*/
        return [
            'mode' => $mode,
            'contact' => $contact
        ];
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

        $contact = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->find($contactId);
        if (false === $contact) {
            $this->addFlash('error', $this->__('Contact could not be found.'));

            return $this->redirectToRoute('zikulaformiculamodule_contact_view');
        }

        // Check for confirmation.
        if (!empty($confirmation)) {
            // Confirm security token code
            $this->checkCsrfToken();        

            $entityManager = $this->get('doctrine')->getManager();
            $entityManager->remove($contact);
            $entityManager->flush();

            // Success
            $this->addFlash('status', $this->__('Contact has been deleted.'));

            return $this->redirectToRoute('zikulaformiculamodule_contact_view');
        }

        return [
            'contact' => $contact
        ];
    }
}
