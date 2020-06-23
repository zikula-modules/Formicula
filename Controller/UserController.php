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

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Bundle\HookBundle\Dispatcher\HookDispatcherInterface;
use Zikula\Bundle\HookBundle\Hook\ValidationHook;
use Zikula\Bundle\HookBundle\Hook\ValidationProviders;
use Zikula\ExtensionsModule\AbstractExtension;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\FormiculaModule\Entity\ContactEntity;
use Zikula\FormiculaModule\Entity\Repository\ContactRepository;
use Zikula\FormiculaModule\Entity\SubmissionEntity;
use Zikula\FormiculaModule\Form\Type\UserSubmissionType;
use Zikula\FormiculaModule\Helper\CaptchaHelper;
use Zikula\FormiculaModule\Helper\EnvironmentHelper;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;

/**
 * Class UserController
 */
class UserController extends AbstractController
{
    /**
     * @var ZikulaHttpKernelInterface
     */
    private $kernel;

    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @var EnvironmentHelper
     */
    private $environmentHelper;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $mailLogger;

    public function __construct(
        AbstractExtension $extension,
        PermissionApiInterface $permissionApi,
        VariableApiInterface $variableApi,
        TranslatorInterface $translator,
        ZikulaHttpKernelInterface $kernel,
        ContactRepository $contactRepository,
        EnvironmentHelper $environmentHelper,
        MailerInterface $mailer,
        LoggerInterface $mailLogger
    ) {
        parent::__construct($extension, $permissionApi, $variableApi, $translator);
        $this->kernel = $kernel;
        $this->contactRepository = $contactRepository;
        $this->environmentHelper = $environmentHelper;
        $this->mailer = $mailer;
        $this->mailLogger = $mailLogger;
    }

    /**
     * Shows a certain form.
     *
     * @Route("/")
     */
    public function indexAction(
        CurrentUserApiInterface $currentUserApi,
        HookDispatcherInterface $hookDispatcher,
        CaptchaHelper $captchaHelper,
        Request $request
    ) {
        $formId = $request->query->getInt('form', (int)$this->getVar('defaultForm', 0));
        $contactId = $request->query->getInt('cid', 0);

        $session = $this->get('session');
        $modVars = $this->getVars();

        $contacts = [];
        $contactChoices = [];
        if ($contactId < 1) {
            $allContacts = $this->contactRepository->findBy([], ['cid' => 'ASC']);

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
            $contacts[] = $this->contactRepository->find($contactId);
        }
        if (0 === count($contacts)) {
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
            $session->remove('formiculaUserData');
        }
        if ($session->has('formiculaCustomFields')) {
            $customFields = unserialize($session->get('formiculaCustomFields'));
            $session->remove('formiculaCustomFields');
        }

        // default user values with an empty form
        $userName = '';
        $emailAddress = '';
        if ($currentUserApi->isLoggedIn()) {
            $userName = $currentUserApi->get('uname');
            $emailAddress = $currentUserApi->get('email');
        }

        $formData = [
            'form' => $formId,
            'adminFormat' => $modVars['defaultAdminFormat'],
            'userFormat' => $modVars['defaultUserFormat'],
            'cid' => $contact->getCid(),
            'name' => $userData['name'] ?? $userName,
            'emailAddress' => $userData['emailAddress'] ?? $emailAddress
        ];
        foreach (['company', 'phone', 'url', 'location', 'comment'] as $fieldName) {
            if ($modVars['show' . ucfirst($fieldName)]) {
                $formData[$fieldName] = $userData[$fieldName] ?? '';
            }
        }

        $form = $this->createForm(
            UserSubmissionType::class,
            $formData,
            [
                'modVars' => $modVars,
                'contactChoices' => $contactChoices,
                'action' => $this->generateUrl('zikulaformiculamodule_user_index')
            ]
        );

        // get additionally provided information - will be passed to the template
        // addinfo is an array: [name1 => value1, name2 => value2]
        $addInfo = $request->query->get('addinfo', []);

        // prepare captcha
        $enableSpamCheck = $captchaHelper->isSpamCheckEnabled($formId);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $formData = $form->getData();
            $formId = $formData['form'];
        }
        if ($form->isSubmitted() && $form->isValid() && $form->get('submit')->isClicked()) {
            $formData = $form->getData();

            // very basic input validation against HTTP response splitting
            $formData['emailAddress'] = str_replace(['\r', '\n', '%0d', '%0a'], '', $formData['emailAddress']);

            $formId = (int) $formData['form'];
            $contactId = $formData['cid'];
            if (empty($contactId) || $formId < 0 || !$this->hasPermission('ZikulaFormiculaModule::', "${formId}:${contactId}:", ACCESS_COMMENT)) {
                throw new AccessDeniedException();
            }

            // generate a return url we need if the form has errors
            $returnUrl = $request->request->get('returntourl', $this->get('router')->generate('zikulaformiculamodule_user_index', ['form' => $formId]));

            $contact = $this->contactRepository->find($contactId);
            if (null === $contact) {
                $this->addFlash('error', 'Contact could not be found.');

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
                $this->addFlash('error', 'Error! No or invalid name given.');
                $hasError = true;
            }

            // uname is needed by the mail templates. Set it now.
            if (!isset($userData['uname']) || empty($userData['uname'])) {
                $userData['uname'] = $userName;
            }
            if (!isset($userData['emailAddress']) || false === filter_var($userData['emailAddress'], FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Error! No or incorrect email address supplied.');
                $hasError = true;
            }

            $customFields = $request->request->get('custom', []);
            foreach ($customFields as $key => $customField) {
                $isMandatory = isset($customField['mandatory']) && 1 === $customField['mandatory'] ? true : false;
                $customFields[$key]['mandatory'] = $isMandatory;
                if ($isMandatory && !is_array($customField['data']) && empty($customField['data'])) {
                    $this->addFlash('error', $this->trans('Error! No value given for mandatory field "%s%".', ['%s%' => $customField['name']]));
                    $hasError = true;
                }
            }

            // check captcha
            if ($enableSpamCheck) {
                $captcha = $request->request->getInt('captcha', 0);
                $operands = @unserialize($session->get('formiculaCaptcha', ''));
                if (is_array($operands)) {
                    $captchaValid = $captchaHelper->isCaptchaValid($operands, $captcha);
                    if (false === $captchaValid) {
                        $this->addFlash('error', 'The calculation to prevent spam was incorrect. Please try again.');
                        $hasError = true;
                    }
                }
            }
            $session->remove('formiculaCaptcha');

            // Check hooked modules for validation
            $validationHook = new ValidationHook(new ValidationProviders());
            $validators = $hookDispatcher->dispatch('zikulaformiculamodule.ui_hooks.forms.validate_edit', $validationHook)->getValidators();
            if ($validators->hasErrors()) {
                $this->addFlash('error', 'The validation of the hooked security module was incorrect. Please try again.');
                $hasError = true;
            }

            $templateParameters = [
                'modVars' => $modVars,
                'contact' => $contact,
                'userData' => $userData,
                'customFields' => $customFields,
                'adminFormat' => $formData['adminFormat'],
                'userFormat' => ($formData['userFormat'] ?? $modVars['defaultUserFormat'])
            ];

            if ($hasError) {
                $session->set('formiculaUserData', serialize($userData));
                $session->set('formiculaCustomFields', serialize($customFields));

                return $this->render('@ZikulaFormiculaModule/Form/' . $formId . '/userError.html.twig', $templateParameters);
            }

            // send emails
            $sentToAdmin = $this->sendMail($request, $contact, $formId, $userData, $customFields, $formData['adminFormat'], 'admin');
            $sentToUser = true;
            if ($modVars['sendConfirmationToUser'] && 'none' !== $formData['userFormat']) {
                $sentToUser = $this->sendMail($request, $contact, $formId, $userData, $customFields, $formData['userFormat'], 'user');
            }

            $templateParameters['sentToUser'] = $sentToUser;

            // store the submitted data in the database
            if (true === $this->getVar('storeSubmissionData', false)) {
                $storeSubmissionDataForms = $this->getVar('storeSubmissionDataForms', '');
                $storeSubmissionDataFormsArray = explode(',', $storeSubmissionDataForms);
                if (empty($storeSubmissionDataForms) || (is_array($storeSubmissionDataFormsArray) && in_array($formId, $storeSubmissionDataFormsArray))) {
                    $submission = new SubmissionEntity();
                    $submission->setForm($formId);
                    $submission->setSid($contact->getCid());
                    $ipAddress = $request->getClientIp();
                    $submission->setIpAddress($ipAddress);
                    $submission->setHostName(gethostbyaddr($ipAddress));
                    $submission->setName($userData['name']);
                    $submission->setEmail($userData['emailAddress']);
                    foreach (['company', 'phone', 'url', 'location', 'comment'] as $fieldName) {
                        if (isset($userData[$fieldName])) {
                            $submission[$fieldName] = $userData[$fieldName];
                        }
                    }
                    foreach ($customFields as $customField) {
                        $submission->addCustomData($customField['name'], $customField['data']);
                    }
                    if (isset($userData['fileUpload'])) {
                        $submission->addCustomData('fileUpload', $userData['fileUpload']);
                    }

                    try {
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($submission);
                        $entityManager->flush();
                    } catch (\Exception $exception) {
                        $this->addFlash('error', $this->trans('Error! Could not store your submission into the database.') . ' ' . $exception->getMessage());
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
     * @return string Name of uploaded file or empty string on failure
     */
    private function handleUpload(UploadedFile $file)
    {
        // Get path to upload directory
        $uploadDirectory = $this->getVar('uploadDirectory', 'public/formicula/uploads');
        // check if it ends with / or we add one
        if ('/' !== mb_substr($uploadDirectory, -1)) {
            $uploadDirectory .= '/';
        }

        $fileName = $file->getClientOriginalName();
        try {
            $file->move($uploadDirectory, $fileName);
        } catch (FileException $exception) {
            $this->addFlash('error', $exception->getMessage());

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
    private function sendMail(
        Request $request,
        ContactEntity $contact,
        $formId,
        array $userData = [],
        array $customFields = [],
        $format = 'html',
        $mailType = ''
    ) {
        if (!$this->kernel->isBundle('ZikulaMailerModule')) {
            // no mailer module - error!
            return false;
        }

        $mailData = $userData;
        if ('plain' === $format && isset($mailData['comment'])) {
            // remove tags from comment to avoid spam
            $mailData['comment'] = strip_tags($mailData['comment']);
        }

        $modVars = $this->getVars();
        $siteName = $this->getVariableApi()->getSystemVar('sitename');
        $adminMail = $this->getVariableApi()->getSystemVar('adminmail');
        $ipAddress = $request->getClientIp();

        // determine subject
        if ('admin' === $mailType) {
            // subject of the emails can be determined from the form
            $subject = isset($userData['adminSubject']) && !empty($userData['adminSubject']) ? $userData['adminSubject'] : $siteName . ' - ' . $contact['name'];
        } elseif ('user' === $mailType) {
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
                $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath() . $request->getPathInfo();
                $subject = str_replace('%s', htmlentities($siteName), $subject);
                $subject = str_replace('%l', htmlentities($this->getVariableApi()->getSystemVar('slogan')), $subject);
                $subject = str_replace('%u', $baseUrl, $subject);
                $subject = str_replace('%c', htmlentities($contact->getSenderName()), $subject);
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

        $message = (new Email())
            ->subject($subject)
            ->text($this->renderView($bodyTemplateText, $templateParameters))
            ->html($this->renderView($bodyTemplateHtml, $templateParameters));

        // add possible attachment to admin mail
        if ('contact' === $mailType && $modVars['showFileAttachment'] && isset($userData['fileUpload'])) {
            // add file attachment
            $uploadDirectory = realpath($this->getVar('uploadDirectory', 'public/formicula/uploads'));
            $message->attachFromPath($uploadDirectory . '/' . $userData['fileUpload']);
        }

        // set sender and recipient
        if ('admin' === $mailType) {
            $fromAddress = true === $modVars['useContactsAsSender'] ? $userData['emailAddress'] : $adminMail;
            $message->from(new Address($fromAddress, $userData['name']));
            $recipients = [];
            if (false !== mb_strpos($contact->getEmail(), ',')) {
                $emails = explode(',', $contact->getEmail());
                foreach ($emails as $email) {
                    $message->addTo(new Address($email, $contact->getName()));
                }
            } else {
                $message->addTo(new Address($contact->getEmail(), $contact->getName()));
            }
        } elseif ('user' === $mailType) {
            $fromName = !empty($contact->getSenderName()) ? $contact->getSenderName() : $siteName . ' - ' . $this->trans('Contact form');
            $fromAddress = !empty($contact->getSenderEmail()) ? $contact->getSenderEmail() : $contact->getEmail();
            $fromAddress = true === $modVars['useContactsAsSender'] ? $fromAddress : $adminMail;
            $message->from(new Address($fromAddress, $fromName));
            $message->addTo(new Address($userData['emailAddress'], $userData['name']));
        }

        // send the email
        $mailSent = true;
        $logging = $this->getVariableApi()->get('ZikulaMailerModule', 'enableLogging', false);
        try {
            $this->mailer->send($message);
            if ($logging) {
                $this->mailLogger->info(sprintf('Email sent to %s', $message->getTo()[0]->getAddress()), [
                    'in' => __METHOD__,
                ]);
            }
        } catch (TransportExceptionInterface $exception) {
            $this->mailLogger->error($exception->getMessage(), [
                'in' => __METHOD__,
            ]);
            $mailSent = false;
        }

        if ('admin' === $mailType) {
            if (true === $modVars['deleteUploadedFiles']) {
                foreach ($message->getAttachments() as $attachment) {
                    if (file_exists($attachment) && is_file($attachment)) {
                        unlink($attachment);
                    }
                }
            }

            if (false === $mailSent) {
                $this->addFlash('error', 'There was an error sending the email to our contact.');
            }
        } elseif ('user' === $mailType) {
            if (false === $mailSent) {
                $this->addFlash('error', 'There was an error sending the confirmation email to your email address.');
            }
        }

        return $mailSent;
    }
}
