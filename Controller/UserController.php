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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\Hook\ValidationHook;
use Zikula\Core\Hook\ValidationProviders;
use Zikula\FormiculaModule\Entity\ContactEntity;
use Zikula\FormiculaModule\Entity\SubmissionEntity;
use Swift_Message;
/**
 * Class UserController
 */
class UserController extends AbstractController
{
    /**
     * Shows a certain form.
     *
     * @Route("/")
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $formId = $request->query->getDigits('form', $this->getVar('defaultForm', 0));
        $contactId = $request->query->getDigits('cid', 0);

        $session = $this->get('session');
        $variableApi = $this->get('zikula_extensions_module.api.variable');
        $modVars = $variableApi->getAll('ZikulaFormiculaModule');

        $contacts = [];
        $contactChoices = [];
        if ($contactId < 1) {
            $allContacts = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->findBy([], ['cid' => 'ASC']);

            // only use those contacts where we have the necessary rights for
            foreach ($allContacts as $contact) {
                $contactId = $contact->getCid();
                if (!$this->hasPermission('ZikulaFormiculaModule::', $formId . ':' . $contactId . ':', ACCESS_COMMENT)) {
                    continue;
                }

                $contacts[] = $contact;
            }
        } else {
            if (!$this->hasPermission('ZikulaFormiculaModule::', $formId . ':' . $contactId . ':', ACCESS_COMMENT)) {
                throw new AccessDeniedException();
            }
            $contacts[] = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->find($contactId);
        }
        if (count($contacts) == 0) {
            throw new AccessDeniedException();
        }
        foreach ($contacts as $contact) {
            if ($contact->isPublic()) {
                $contactChoices[$contact['name']] = $contact['cid'];
            }
        }

        $userData = [];
        $customFields = [];
        if ($session->has('formiculaUserData')) {
            $userData = unserialize($session->get('formiculaUserData'));
            $session->del('formiculaUserData');
        }
        if ($session->has('formiculaCustomFields')) {
            $customFields = unserialize($session->get('formiculaCustomFields'));
            $session->del('formiculaCustomFields');
        }

        // default user values with an empty form
        $currentUserApi = $this->get('zikula_users_module.current_user');
        $userName = '';
        $emailAddress = '';
        if ($currentUserApi->isLoggedIn()) {
            $userName = \UserUtil::getVar('name') != '' ? \UserUtil::getVar('name') : $currentUserApi->get('uname');
            $emailAddress = $currentUserApi->get('email');
        }

        $formData = [
            'form' => $formId,
            'adminFormat' => $modVars['defaultAdminFormat'],
            'userFormat' => $modVars['defaultUserFormat'],
            'cid' => $contact->getCid(),
            'name' => isset($userData['name']) ? $userData['name'] : $userName,
            'emailAddress' => isset($userData['emailAddress']) ? $userData['emailAddress'] : $emailAddress
        ];
        foreach (['company', 'phone', 'url', 'location', 'comment'] as $fieldName) {
            if ($modVars['show' . ucfirst($fieldName)]) {
                $formData[$fieldName] = isset($userData[$fieldName]) ? $userData[$fieldName] : '';
            }
        }

        $form = $this->createForm('Zikula\FormiculaModule\Form\Type\UserSubmissionType',
            $formData, [
                'translator' => $this->get('translator.default'),
                'modVars' => $modVars,
                'contactChoices' => $contactChoices
            ]
        );

        // get additionally provided information - will be passed to the template
        // addinfo is an array: [name1 => value1, name2 => value2]
        $addInfo = $request->query->get('addinfo', []);

        // prepare captcha
        $captchaHelper = $this->get('zikula_formicula_module.helper.captcha_helper');
        $enableSpamCheck = $captchaHelper->isSpamCheckEnabled($formId);

        if ($form->handleRequest($request)->isValid() && $form->get('submit')->isClicked()) {
            $formData = $form->getData();

            // very basic input validation against HTTP response splitting
            $formData['emailAddress'] = str_replace(['\r', '\n', '%0d', '%0a'], '', $formData['emailAddress']);

            $formId = intval($formData['form']);
            $contactId = $formData['cid'];
            if (empty($contactId) || $formId < 0 || !$this->hasPermission('ZikulaFormiculaModule::', "$formId:$contactId:", ACCESS_COMMENT)) {
                throw new AccessDeniedException();
            }

            // generate a return url we need if the form has errors
            $returnUrl = $request->request->get('returntourl', $this->get('router')->generate('zikulaformiculamodule_user_index', ['form' => $formId]));

            $contact = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->find($contactId);
            if (null === $contact) {
                $this->addFlash('error', $this->__('Contact could not be found.'));

                return $this->redirect($returnUrl);
            }

            $userData = [];
            foreach ($formData as $fieldName => $value) {
                if (in_array($fieldName, ['form', 'adminFormat', 'userFormat', 'cid'])) {
                    continue;
                }
                $userData[$fieldName] = $value;
            }

            $hasError = false;

            if ($modVars['showFileAttachment'] && isset($userData['fileUpload'])) {
                $userData['fileUpload'] = $this->handleUpload($userData['fileUpload']);
                if (!$userData['fileUpload']) {
                    $hasError = true;
                }
            }

            if (!isset($userData['name']) || empty($userData['name'])) {
                $this->addFlash('error', $this->__('Error! No or invalid name given.'));
                $hasError = true;
            }
            
            // uname is needed by the mail templates. Set it now.
            if (!isset($userData['uname']) || empty($userData['uname'])) {
                $userData['uname'] = $userName;
            }
            if (!isset($userData['emailAddress']) || false === filter_var($userData['emailAddress'], FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', $this->__('Error! No or incorrect email address supplied.'));
                $hasError = true;
            }

            $customFields = $request->request->get('custom', []);
            foreach ($customFields as $key => $customField) {
                $isMandatory = isset($customField['mandatory']) && $customField['mandatory'] == 1 ? true : false;
                $customFields[$key]['mandatory'] = $isMandatory;
                if ($isMandatory && !is_array($customField['data']) && empty($customField['data'])) {
                    $this->addFlash('error', $this->__f('Error! No value given for mandatory field "%s".', ['%s' => $customField['name']]));
                }
            }

            // check captcha
            if ($enableSpamCheck) {
                $captcha = (int)$request->request->getDigits('captcha', 0);
                $operands = @unserialize($session->get('formiculaCaptcha'));
                $captchaValid = $captchaHelper->isCaptchaValid($operands, $captcha);
                if (false === $captchaValid) {
                    $this->addFlash('error', $this->__('The calculation to prevent spam was incorrect. Please try again.'));
                    $hasError = true;
                }
            }
            $session->del('formiculaCaptcha');

            // Check hooked modules for validation
            $validationHook = new ValidationHook(new ValidationProviders());
            $validators = $this->get('hook_dispatcher')->dispatch('formicula.ui_hooks.forms.validate_edit', $validationHook)->getValidators();
            if ($validators->hasErrors()) {
                $this->addFlash('error', $this->__('The validation of the hooked security module was incorrect. Please try again.'));
                $hasError = true;
            }

            $templateParameters = [
                'modVars' => $modVars,
                'contact' => $contact,
                'userData' => $userData,
                'customFields' => $customFields
            ];

            if ($hasError) {
                $session->set('formiculaUserData', serialize($userData));
                $session->set('formiculaCustomFields', serialize($customFields));

                return $this->render('@ZikulaFormiculaModule/Form/' . $formId . '/userError.html.twig', $templateParameters);
            }

            // send emails
            $sentToAdmin = $this->sendMail($request, $contact, $formId, $userData, $customFields, $formData['adminFormat'], 'admin');
            $sentToUser = true;
            if ($modVars['sendConfirmationToUser'] && $formData['userFormat'] != 'none') {
                $sentToUser = $this->sendMail($request, $contact, $formId, $userData, $customFields, $formData['userFormat'], 'user');
            }

            $templateParameters['sentToUser'] = $sentToUser;

            // store the submitted data in the database
            if (true === $this->getVar('storeSubmissionData', false)) {
                $storeSubmissionDataForms = $this->getVar('storeSubmissionDataForms', '');
                $storeSubmissionDataFormsArray = explode(',', $storeSubmissionDataForms);
                if (empty($storeSubmissionDataForms) || (is_array($storeSubmissionDataFormsArray) && in_array($formId, $storeSubmissionDataFormsArray))) {
                    $submisssion = new SubmissionEntity();
                    $submisssion->setForm($formId);
                    $submisssion->setSid($contact->getCid());
                    $ipAddress = $request->getClientIp();
                    $submisssion->setIpAddress($ipAddress);
                    $submisssion->setHostName(gethostbyaddr($ipAddress));
                    $submisssion->setName($userData['name']);
                    $submisssion->setEmail($userData['emailAddress']);
                    foreach (['company', 'phone', 'url', 'location', 'comment'] as $fieldName) {
                        if (isset($userData[$fieldName])) {
                            $submisssion[$fieldName] = $userData[$fieldName];
                        }
                    }
                    foreach ($customFields as $customField) {
                        $submission->addCustomData($customField['name'], $customField['data']);
                    }
                    if (isset($userData['fileUpload'])) {
                        $submission->addCustomData('fileUpload', $userData['fileUpload']);
                    }

                    try {
                        $entityManager = $this->get('doctrine')->getManager();
                        $entityManager->persist($submission);
                        $entityManager->flush();
                    } catch (Exception $e) {
                        $this->addFlash('error', $this->__('Error! Could not store your submission into the database.') . ' ' . $e->getMessage());
                    }
                }
            }

            return $this->render('@ZikulaFormiculaModule/Form/' . $formId . '/userConfirm.html.twig', $templateParameters);
        }

        // show the form
        $templateParameters = [
            'modVars' => $modVars,
            'form' => $form->createView(),
            'customFields' => $customFields,
            'addInfo' => $addInfo,
            'enableSpamCheck' => $enableSpamCheck
        ];

        return $this->render('@ZikulaFormiculaModule/Form/' . $formId . '/userForm.html.twig', $templateParameters);
    }

    /**
     * Processes a possible file upload.
     *
     * @param UploadedFile $file The uploaded file
     *
     * @return string Name of uploaded file or empty string on failure
     */
    private function handleUpload(UploadedFile $file)
    {
        // Get path to upload directory
        $uploadDirectory = $this->getVar('uploadDirectory', 'userdata');
        // check if it ends with / or we add one
        if (substr($uploadDirectory, -1) != '/') {
            $uploadDirectory .= '/';
        }

        $fileName = $file->getClientOriginalName();
        try {
            $file = $file->getData()->move($uploadDirectory, $fileName);
        } catch (FileException $e) {
            $this->addFlash('error', $e->getMessage());

            return '';
        }

        return $fileName;
    }

    /**
     * Sends mails to the contact and, if configured, to the user.
     *
     * @param Request       $request      Current request instance
     * @param ContactEntity $contact      The contact entity
     * @param integer       $formId       The form number
     * @param array         $userData     Input for base fields
     * @param array         $customFields Input for additional fields
     * @param string        $format       Email format to user
     * @param string        $mailType     Type of mail to send (admin or user)
     *
     * @return boolean True if mail was successfully sent, false otherwise
     */
    private function sendMail(Request $request, ContactEntity $contact, $formId, array $userData = [], array $customFields = [], $format = 'html', $mailType = '')
    {
        if (!$this->get('kernel')->isBundle('ZikulaMailerModule')) {
            // no mailer module - error!
            return false;
        }

        $mailData = $userData;
        if ($format == 'plain') {
            // remove tags from comment to avoid spam
            $mailData['comment'] = strip_tags($mailData['comment']);
        }

        $formId = \DataUtil::formatForOS($formId);
        $variableApi = $this->get('zikula_extensions_module.api.variable');
        $modVars = $variableApi->getAll('ZikulaFormiculaModule');
        $siteName = $variableApi->get('ZConfig', 'sitename');
        $adminMail = $variableApi->get('ZConfig', 'adminmail');
        $ipAddress = $request->getClientIp();

        // determine subject
        if ($mailType == 'admin') {
            // subject of the emails can be determined from the form
            $subject = isset($userData['adminSubject']) && !empty($userData['adminSubject']) ? $userData['adminSubject'] : $siteName . ' - ' . $contact['name'];
        } elseif ($mailType == 'user') {
            // check for subject, can be in the form or in the contact
            if (!empty($contact->getSendingSubject()) || !empty($userData['userSubject'])) {
                $subject = !empty($userData['userSubject']) ? $userData['userSubject'] : $contact->getSendingSubject();
                // replace some placeholders
                // %s = sitename
                // %l = slogan
                // %u = site url
                // %c = contact name
                // %n<num> = user defined field name <num>
                // %d<num> = user defined field data <num>
                $subject = str_replace('%s', \DataUtil::formatForDisplay($siteName), $subject);
                $subject = str_replace('%l', \DataUtil::formatForDisplay($variableApi->get('ZConfig', 'slogan')), $subject);
                $subject = str_replace('%u', \System::getBaseUrl(), $subject);
                $subject = str_replace('%c', \DataUtil::formatForDisplay($contact->getSenderName()), $subject);
                $i = 0;
                foreach ($customFields as $fieldName => $customField) {
                    $i++;
                    $subject = str_replace('%n' . $i, $customField['name'], $subject);
                    $subject = str_replace('%d' . $i, $customField['data'], $subject);
                }
            } else {
                $subject = $siteName . ' - ' . $contact->getName();
            }
        }

        // determine body
        $templateParameters = [
             'ipAddress' => $ipAddress,
             'hostName' => gethostbyaddr($ipAddress),
             'form' => $formId,
             'contact' => $contact,
             'userData' => $userData,
             'customFields' => $customFields,
             'siteName' => $siteName,
             'modVars' => $modVars
        ];

        $bodyTemplateHtml = '@ZikulaFormiculaModule/Form/' . $formId . '/' . $mailType . 'Mail.html.twig';
        $bodyTemplateText = '@ZikulaFormiculaModule/Form/' . $formId . '/' . $mailType . 'Mail.txt.twig';
        $bodyHtml = $this->renderView($bodyTemplateHtml, $templateParameters);
        $bodyText = $this->renderView($bodyTemplateText, $templateParameters);

        $body = '';
        $altBody = '';
        if ($format == 'text') {
            $body = $bodyText;
        } elseif ($format == 'html') {
            $body = $bodyHtml;
            $altBody = $bodyText;
        }

        // add possible attachment to admin mail
        $attachments = [];
        if ($mailType == 'contact' && $modVars['showFileAttachment'] && isset($userData['fileUpload'])) {
            // add file attachment
            $uploadDirectory = realpath($variableApi->get('ZikulaFormiculaModule', 'uploadDirectory', 'userdata'));
            $attachments[] = $uploadDirectory . '/' . $userData['fileUpload'];
        }

        // create new message instance
        /** @var Swift_Message */
        $message = Swift_Message::newInstance();

        // set sender and recipient
        if ($mailType == 'admin') {
            $fromAddress = true === $modVars['useContactsAsSender'] ? $userData['emailAddress'] : $adminMail;
            $message->setFrom([$fromAddress => $userData['name']]);
            $message->setTo([$contact->getEmail() => $contact->getName()]);
        } elseif ($mailType == 'user') {
            $fromName = !empty($contact->getSenderName()) ? $contact->getSenderName() : $siteName . ' - ' . $this->__('Contact form');
            $fromAddress = !empty($contact->getSenderEmail()) ? $contact->getSenderEmail() : $contact->getEmail();
            $fromAddress = true === $modVars['useContactsAsSender'] ? $fromAddress : $adminMail;
            $message->setFrom([$adminMail => $fromName]);
            $message->setTo([$userData['emailAddress'] => $userData['name']]);
        }

        // send the email
        $mailer = $this->get('zikula_mailer_module.api.mailer');
        $mailSent = $mailer->sendMessage($message, $subject, $body, $altBody, ($format == 'html'), [], $attachments);

        if ($mailType == 'admin') {
            if (true === $modVars['deleteUploadedFiles']) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment) && is_file($attachment)) {
                        unlink($attachment);
                    }
                }
            }

            if (false === $mailSent) {
                $this->addFlash('error', $this->__('There was an error sending the email to our contact.'));
            }
        } elseif ($mailType == 'user') {
            if (false === $mailSent) {
                $this->addFlash('error', $this->__('There was an error sending the confirmation email to your email address.'));
            }
        }

        return $mailSent;
    }
}
