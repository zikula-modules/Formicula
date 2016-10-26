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
        $form = (int)FormUtil::getPassedValue('form', $this->getVar('defaultForm', 0), 'GET');
        $contactId = (int)FormUtil::getPassedValue('cid', -1, 'GET');

        $customFields = unserialize(SessionUtil::getVar('formiculaCustomFields'));
        $userdata = unserialize(SessionUtil::getVar('formiculaUserData'));
        SessionUtil::delVar('formiculaCustomFields');
        SessionUtil::delVar('formiculaUserData');
        
        // get submitted information - will be passed to the template
        // addinfo is an array:
        // addinfo[name1] = value1
        // addinfo[name2] = value2
        $addinfo = FormUtil::getPassedValue('addinfo', [], 'GET');

        // reset captcha
        SessionUtil::delVar('formiculaCaptcha');

        $contacts = [];
        if ($contactId == -1) {
            $contacts = ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'readValidContacts', ['form' => $form]);
        } else {
            if (!$this->hasPermission('ZikulaFormiculaModule::', $form . ':' . $contactId . ':', ACCESS_COMMENT)) {
                throw new AccessDeniedException();
            }

            $contacts[] = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->find($contactId);
        }

        if (count($contacts) == 0) {
            throw new AccessDeniedException();
        }

        // default user values with an empty form
        $uname = '';
        $uemail = '';
        if (UserUtil::isLoggedIn()) {
            $uname = UserUtil::getVar('name') != '' ? UserUtil::getVar('name') : UserUtil::getVar('uname');
            $uemail = UserUtil::getVar('email');
        }

        $enableSpamCheck = $this->get('zikula_formicula_module.helper.captcha_helper')->isSpamCheckEnabled($form);

        if (empty($userData)) {
            $userData = [
                'uname' => $uname,
                'uemail' => $uemail,
                'comment' => '',
                'url' => '',
                'phone' => '',
                'company' => '',
                'location' => ''
            ];
        }

        $templateParameters = [
            'customFields' => $customFields,
            'userData' => $userData,
            'contacts' => $contacts,
            'addinfo' => $addinfo,
            'enableSpamCheck' => $enableSpamCheck
        ];

        $this->view->assign($templateParameters);

        return $this->view->fetch('Form/' . $form . '/userForm.html.twig');
    }

    /**
     * Sends the mail to the contact and, if configured, to the user.
     *
     * @Route("/send")
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return RedirectResponse
     */
    public function sendAction(Request $request)
    {
        $form = (int)FormUtil::getPassedValue('form', $this->getVar('defaultForm', 0), 'POST');
        $contactId = (int)FormUtil::getPassedValue('cid', 0, 'POST');
        $captcha = (int)FormUtil::getPassedValue('captcha', 0, 'POST');
        $userFormat = FormUtil::getPassedValue('userFormat', $this->getVar('defaultUserFormat', 'html'),  'POST');
        $adminFormat = FormUtil::getPassedValue('adminFormat', $this->getVar('defaultAdminFormat', 'html'), 'POST');
        $errorReturnUrl = FormUtil::getPassedValue('returntourl', '',  'POST');

        if (!$this->hasPermission('ZikulaFormiculaModule::', "$form:$contactId:", ACCESS_COMMENT)) {
            throw new AccessDeniedException();
        }

        if ($errorReturnUrl == '') {
            // generate a returnurl we need if the form has errors
            $errorReturnUrl = ModUtil::url('ZikulaFormiculaModule', 'user', 'main', ['form' => $form]);
        }

        // Confirm security token code
        $this->checkCsrfToken();

        if (empty($contactId) && empty($form)) {
            return System::redirect(System::getHomepageUrl());
        }
        
        $userData = [];
        $customFields = [];
        // Upload directory
        $uploadDirectory = $this->getVar('uploadDirectory', 'userdata');
        // check if it ends with / or we add one
        if (substr($uploadDirectory, -1) != '/') {
            $uploadDirectory .= '/';
        }

        $userData = FormUtil::getPassedValue('userData', (isset($args['userData'])) ? $args['userData'] : [], 'GETPOST');
        $customFields   = FormUtil::getPassedValue('custom', (isset($args['custom'])) ? $args['custom'] : [], 'GETPOST');
        $userData['uname']    = isset($userData['uname']) ? $userData['uname'] : '';
        $userData['uemail']   = isset($userData['uemail']) ? $userData['uemail'] : '';
        $userData['url']      = isset($userData['url']) ? $userData['url'] : '';
        $userData['phone']    = isset($userData['phone']) ? $userData['phone'] : '';
        $userData['company']  = isset($userData['company']) ? $userData['company'] : '';
        $userData['location'] = isset($userData['location']) ? $userData['location'] : '';
        $userData['comment']  = isset($userData['comment']) ? $userData['comment'] : '';

        foreach ($customFields as $k => $customField) {
            $customField['mandatory'] = ($customField['mandatory'] == 1) ? true : false;

            // get uploaded files
            if (isset($_FILES['custom']['tmp_name'][$k]['data'])) {
                $customFields[$k]['data']['error'] = $_FILES['custom']['error'][$k]['data'];
                if ($customField['data']['error'] == 0) {
                    $customField['data']['size'] = $_FILES['custom']['size'][$k]['data'];
                    $customField['data']['type'] = $_FILES['custom']['type'][$k]['data'];
                    $customField['data']['name'] = $_FILES['custom']['name'][$k]['data'];
                    $customField['upload'] = true;
                    move_uploaded_file($_FILES['custom']['tmp_name'][$k]['data'], DataUtil::formatForOS($uploadDirectory . $customField['data']['name']));
                } else {
                    // error - replace the 'data' with an errormessage
                    $customField['data'] = constant('_FOR_UPLOADERROR' . $customField['data']['error']);
                }
            } else {
                $customField['upload'] = false;
            }
            $customFields[$k] = $customField;
        }
        
        // check captcha
        $captchaHelper = $this->get('zikula_formicula_module.helper.captcha_helper');
        $enableSpamCheck = $captchaHelper->isSpamCheckEnabled($form);
        if ($enableSpamCheck) {
            $operands = @unserialize(SessionUtil::getVar('formiculaCaptcha'));
            $captchaValid = $captchaHelper->isCaptchaValid($operands, $captcha);
            if (false === $captchaValid) {
                SessionUtil::delVar('formiculaCaptcha');
                $params = ['form' => $form];
                if (is_array($addinfo) && count($addinfo) > 0) {
                    $params['addinfo'] = $addinfo;
                }
                SessionUtil::setVar('formiculaUserData', serialize($userData));
                SessionUtil::setVar('formiculaCustomFields', serialize($customFields));

                return LogUtil::registerError($this->__('The calculation to prevent spam was incorrect. Please try again.'), null, $errorReturnUrl);
            }
        }
        SessionUtil::delVar('formiculaCaptcha');

        // Check hooked modules for validation
        $hookvalidators = $this->notifyHooks(new Zikula_ValidationHook('formicula.ui_hooks.forms.validate_edit', new Zikula_Hook_ValidationProviders()))->getValidators();
        if ($hookvalidators->hasErrors()) {
            SessionUtil::setVar('formiculaUserData', serialize($userData));
            SessionUtil::setVar('formiculaCustomFields', serialize($customFields));

            return LogUtil::registerError($this->__('The validation of the hooked security module was incorrect. Please try again.'), null, $errorReturnUrl);
        }

        $params = ['form' => $form];
        if (isset($addinfo) && is_array($addinfo) && count($addinfo) > 0) {
            $params['addinfo'] = $addinfo;
        }

        if (empty($userFormat) || !in_array($userFormat, ['plain', 'html', 'none'])) {
            $userFormat = 'plain';
        }
        if (empty($adminFormat) || !in_array($adminFormat, ['plain', 'html'])) {
            $adminFormat = 'plain';
        }

        // very basic input validation against HTTP response splitting
        $userData['uemail'] = str_replace(['\r', '\n', '%0d', '%0a'], '', $userData['uemail']);

        $contact = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->find($contactId);

        $templateParameters = [
            'contact' => $contact,
            'userData' => $userData,
            'userFormat' => $userFormat,
            'adminFormat' => $adminFormat
        ];
        $this->view->assign($templateParameters);

        $argumentsValid = ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'checkArguments', [
            'userData'     => $userData,
            'customFields' => $customFields,
            'userFormat'   => $userFormat
        ]);

        if (true === $argumentsValid) {
            $userDataComment = $userData['comment'];

            if ($adminFormat == 'plain') {
                // remove tags from comment to avoid spam
                $userData['comment'] = strip_tags($userDataComment);
            }
            // send the submitted data to the contact(s)
            if (ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'sendtoContact', [
                    'contact'      => $contact,
                    'userData'     => $userData,
                    'customFields' => $customFields,
                    'form'         => $form,
                    'format'       => $adminFormat
            ]) == false) {
                return LogUtil::registerError($this->__('There was an error sending the email.'), null, $errorReturnUrl);
            }

            if ($userFormat == 'plain') {
                // remove tags from comment to avoid spam
                $userData['comment'] = strip_tags($userDataComment);
            }
            // send the submitted data as confirmation to the user
            if ($this->getVar('send_user') == 1 && $userFormat != 'none') {
                // we replace the array of data of uploaded files with the filename
                $this->view->assign('sentToUser', ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'sendtoUser', [
                    'contact'      => $contact,
                    'userData'     => $userData,
                    'customFields' => $customFields,
                    'form'         => $form,
                    'format'       => $userFormat
                ]));
            }

            // store the submitted data in the database
            if (true === $this->getVar('storeSubmissionData', false)) {
                $storeSubmissionDataForms = $this->getVar('storeSubmissionDataForms', '');
                $storeSubmissionDataFormsArray = explode(',', $storeSubmissionDataForms);
                if (empty($storeSubmissionDataForms) || (is_array($storeSubmissionDataFormsArray) && in_array($form, $storeSubmissionDataFormsArray))) {
                    ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'storeInDatabase', [
                        'contact'      => $contact,
                        'userData'     => $userData,
                        'customFields' => $customFields,
                        'form'         => $form
                    ]);
                }
            }
        }

        $customFields = ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'removeUploadInformation', ['customFields' => $customFields]);
        $this->view->assign('customFields', $customFields);

        $template = $argumentsValid ? 'userConfirm' : 'userError';

        return $this->view->fetch('Form/' . $form . '/' . $template . '.html.twig');
    }
}
