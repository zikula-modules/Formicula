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
     * Main entry point for the user.
     *
     * @param form int number of form to show
     * @param owncontacts array of own contacts to replace with the standard. The array can contain the following values
     *    name the contact full name (required)
     *    sname the contact secure name wich will be send to the submitter (optional)
     *    email the contact email (required)
     *    semail the contact email wich will be send to the submiter (optional)
     *    ssubject the subject of the confirmation mail (optional)
     * @return view output
     */
    public function indexAction(Request $request)
    {
        $default_form = $this->getVar('default_form', 0);
        $form = (int)FormUtil::getPassedValue('form', (isset($args['form'])) ? $args['form'] : $default_form, 'GETPOST');
        $cid  = (int)FormUtil::getPassedValue('cid',  (isset($args['cid'])) ? $args['cid'] : -1,  'GETPOST');

        $customFields = unserialize(SessionUtil::getVar('formicula_customFields'));
        $userdata = unserialize(SessionUtil::getVar('formicula_userdata'));
        SessionUtil::delVar('formicula_customFields');
        SessionUtil::delVar('formicula_userdata');
        
        // get submitted information - will be passed to the template
        // addinfo is an array:
        // addinfo[name1] = value1
        // addinfo[name2] = value2
        $addinfo  = FormUtil::getPassedValue('addinfo',  (isset($args['addinfo'])) ? $args['addinfo'] : [],  'GETPOST');

        // reset captcha
        SessionUtil::delVar('formiculaCaptcha');

        $ownContacts = false;
        $ownContactsUse = FormUtil::getPassedValue('ownContacts', -1, 'GETPOST');
        if (isset($args['ownContacts']) && is_array($args['ownContacts']) && $ownContactsUse != -1) {
            $contacts = $args['ownContacts'];
            $id = ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'addSessionOwncontacts', ['ownContacts' => $args['ownContacts']]);
            SessionUtil::setVar('formiculaOwnContactsUse', $id);
            $ownContacts = true;
        } elseif (null !== SessionUtil::getVar('formiculaOwnContacts', null) && $ownContactsUse != -1) {
            $sessionContacts = SessionUtil::getVar('formiculaOwnContacts');
            $contacts = $sessionContacts[$ownContactsUse];
            if (!ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'checkOwncontacts', ['ownContacts' => $contacts])) {
                return false;
            }
            SessionUtil::setVar('formiculaOwnContactsUse', $ownContactsUse);
            $ownContacts = true;
        } elseif ($cid == -1) {
            $contacts = ModUtil::apiFunc('Formicula', 'user', 'readValidContacts', ['form' => $form]);
        } else {
            $contacts[] = ModUtil::apiFunc('Formicula', 'user', 'getContact', [
                'cid'  => $cid,
                'form' => $form
            ]);
        }

        if (true === $ownContacts) {
            if (!SecurityUtil::checkPermission('Formicula::OwnContacts', "$form::", ACCESS_COMMENT)) {
                return LogUtil::registerPermissionError(System::getHomepageUrl());
            }
            foreach ($contacts as $key => $item) {
                $contacts[$key]['cid'] = $key+1;
                $contacts[$key]['public'] = 1;
            }
        } else {
            SessionUtil::delVar('formiculaOwnContactsUse');
        }

        if (count($contacts) == 0) {
            return LogUtil::registerPermissionError(System::getHomepageUrl());
        }

        // default user values with an empty form
        $uname = '';
        $uemail = '';
        if (UserUtil::isLoggedIn()) {
            $uname = UserUtil::getVar('name') != '' ? UserUtil::getVar('name') : UserUtil::getVar('uname');
            $uemail = UserUtil::getVar('email');
        }

        $spamcheck = $this->getVar('spamcheck');
        if ($spamcheck == 1) {
            // Split the list of formids to exclude from spam checking into an array
            $excludespamcheck = explode(',', $this->getVar('excludespamcheck'));
            if (is_array($excludespamcheck) && array_key_exists($form, array_flip($excludespamcheck))) {
                $spamcheck = 0;
            }
        }

        $this->view->add_core_data()->setCaching(false);
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

        $this->view->assign('customFields', $customFields)
                   ->assign('userData', $userData)
        // for BC also provide uname and uemail
                   ->assign('uname', $uname)
                   ->assign('uemail', $uemail)
                   ->assign('contacts', $contacts)
                   ->assign('addinfo', $addinfo)
                   ->assign('spamcheck', $spamcheck);

        return $this->view->fetch('forms' . DIRECTORY_SEPARATOR . $form.'_userform.tpl');
    }

    /**
     * send
     * sends the mail to the contact and, if configured, to the user and dbase
     *
     * @param cid         int contact id
     * @param form        int form id
     * @param userFormat  string email format for user, either 'plain' (default) or 'html'
     * @param adminFormat string email format for admin, either 'plain' (default) or 'html'
     * @param dataformat  string form fields format, either 'plain' (default) or 'array'
     * @param formdata    array  forms fields in array format if configured in dataformat
     * @param uname       string users name
     * @param uemail      string users email
     * @param url         string users homepage
     * @param phone       string users phone
     * @param company     string users company
     * @param location    string users location
     * @param comment     string users comment
     * @return view output
     */
    public function sendAction(Request $request)
    {
        $form           = (int)FormUtil::getPassedValue('form',        (isset($args['form'])) ? $args['form'] : 0, 'GETPOST');
        $cid            = (int)FormUtil::getPassedValue('cid',         (isset($args['cid'])) ? $args['cid'] : 0,  'GETPOST');
        $captcha        = (int)FormUtil::getPassedValue('captcha',     (isset($args['captcha'])) ? $args['captcha'] : 0, 'GETPOST');
        $userFormat     =      FormUtil::getPassedValue('userFormat',  (isset($args['userFormat'])) ? $args['userFormat'] : 'plain',  'GETPOST');
        $adminFormat    =      FormUtil::getPassedValue('adminFormat', (isset($args['adminFormat'])) ? $args['adminFormat'] : 'plain', 'GETPOST');
        $dataformat     =      FormUtil::getPassedValue('dataformat',  (isset($args['dataformat'])) ? $args['dataformat'] : 'plain', 'GETPOST');
        $returntourl    =      FormUtil::getPassedValue('returntourl', (isset($args['returntourl'])) ? $args['returntourl'] : '',  'GETPOST');
        //get the useOwnContacts var
        $ownContactsUse = SessionUtil::getVar('formiculaOwnContactsUse', -1);
        //generate a returnurl we need if the form has errors
        $errorReturnUrl = $ownContactsUse == -1
            ? ModUtil::url('Formicula', 'user', 'main', ['form' => $form])
            : ModUtil::url('Formicula', 'user', 'main', ['form' => $form, 'ownContacts' => $ownContactsUse])
        ;

        // Confirm security token code
        $this->checkCsrfToken();

        if (empty($cid) && empty($form)) {
            return System::redirect(System::getHomepageUrl());
        }
        
        $userData = [];
        $customFields = [];
        // Upload directory
        $uploaddir = $this->getVar('upload_dir');
        // check if it ends with / or we add one
        if (substr($uploaddir, strlen($uploaddir)-1, 1) <> "/") {
            $uploaddir .= "/";
        }
        if ($dataformat == 'array') {
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
                        move_uploaded_file($_FILES['custom']['tmp_name'][$k]['data'], DataUtil::formatForOS($uploaddir . $customField['data']['name']));
                    } else {
                        // error - replace the 'data' with an errormessage
                        $customField['data'] = constant('_FOR_UPLOADERROR' . $customField['data']['error']);
                    }
                } else {
                    $customField['upload'] = false;
                }
                $customFields[$k] = $customField;
            }
        } else {
            $userData['uname']    = FormUtil::getPassedValue('uname',     (isset($args['uname'])) ? $args['uname'] : '', 'GETPOST');
            $userData['uemail']   = FormUtil::getPassedValue('uemail',    (isset($args['uemail'])) ? $args['uemail'] : '',  'GETPOST');
            $userData['url']      = FormUtil::getPassedValue('url',       (isset($args['url'])) ? $args['url'] : '', 'GETPOST');
            $userData['phone']    = FormUtil::getPassedValue('phone',     (isset($args['phone'])) ? $args['phone'] : '',  'GETPOST');
            $userData['company']  = FormUtil::getPassedValue('company',   (isset($args['company'])) ? $args['company'] : '', 'GETPOST');
            $userData['location'] = FormUtil::getPassedValue('location',  (isset($args['location'])) ? $args['location'] : '',  'GETPOST');
            $userData['comment']  = FormUtil::getPassedValue('comment',   (isset($args['comment'])) ? $args['comment'] : '', 'GETPOST');

            // we read custom fields until we find three missing indices in a row
            $i = 0;
            $missing = 0;
            do {
                $customFields[$i]['name'] = FormUtil::getPassedValue('custom'.$i.'name', null, 'POST');
                if ($customFields[$i]['name'] == null) {
                    // increase the number of missing indices and clear this custom var
                    $missing++;
                    unset($customFields[$i]);
                } else {
                    $customFields[$i]['mandatory'] = (FormUtil::getPassedValue('custom'.$i.'mandatory') == 1) ? true : false;
    
                    // get uploaded files
                    if (isset($_FILES['custom'.$i.'data']['tmp_name'])) {
                        $customFields[$i]['data']['error'] = $_FILES['custom'.$i.'data']['error'];
                        if ($customFields[$i]['data']['error'] == 0) {
                            $customFields[$i]['data']['size']     = $_FILES['custom'.$i.'data']['size'];
                            $customFields[$i]['data']['type']     = $_FILES['custom'.$i.'data']['type'];
                            $customFields[$i]['data']['name']     = $_FILES['custom'.$i.'data']['name'];
                            $customFields[$i]['upload'] = true;
                            move_uploaded_file($_FILES['custom'.$i.'data']['tmp_name'], DataUtil::formatForOS($uploaddir.$customFields[$i]['data']['name']));
                        } else {
                            // error - replace the 'data' with an errormessage
                            $customFields[$i]['data'] = constant("_FOR_UPLOADERROR".$customFields[$i]['data']['error']);
                        }
                    } else {
                        $customFields[$i]['data'] = FormUtil::getPassedValue('custom'.$i.'data');
                        $customFields[$i]['upload'] = false;
                    }
                    // reset the errorcounter if an existing field is found
                    $missing = 0;
                    // increase the counter
                    // $i++;
                }
                // increase the counter
                $i++;
            } while ($missing < 3);
        }
        
        // check captcha
        $spamcheck = $this->getVar('spamcheck');
        if ($spamcheck == 1) {
            $excludespamcheck = explode(',', $this->getVar('excludespamcheck'));
            if (is_array($excludespamcheck) && array_key_exists($form, array_flip($excludespamcheck))) {
                $spamcheck = 0;
            }
        }
        if ($spamcheck == 1) {
            $captcha_ok = false;
            $operands = @unserialize(SessionUtil::getVar('formiculaCaptcha'));
            if (is_array($operands)) {
                switch ($operands['z'] . '-' . $operands['w']) {
                    case '0-0':
                        $captcha_ok = (((int)$operands['x'] + (int)$operands['y'] + (int)$operands['v']) == $captcha);
                        break;
                    case '0-1':
                        $captcha_ok = (((int)$operands['x'] + (int)$operands['y'] - (int)$operands['v']) == $captcha);
                        break;
                    case '1-0':
                        $captcha_ok = (((int)$operands['x'] - (int)$operands['y'] + (int)$operands['v']) == $captcha);
                        break;
                    case '1-1':
                        $captcha_ok = (((int)$operands['x'] - (int)$operands['y'] - (int)$operands['v']) == $captcha);
                        break;
                    default:
                    // $captcha_ok is false
                }
            }

            if ($captcha_ok == false) {
                SessionUtil::delVar('formiculaCaptcha');
                // todo: append params to $returntourl and redirect, see ticket #44
                $params = ['form' => $form];
                if (is_array($addinfo) && count($addinfo) > 0) {
                    $params['addinfo'] = $addinfo;
                }
                SessionUtil::setVar('formicula_userData', serialize($userData));
                SessionUtil::setVar('formicula_customFields', serialize($customFields));

                return LogUtil::registerError($this->__('The calculation to prevent spam was incorrect. Please try again.'), null, $errorReturnUrl);
            }
        }
        SessionUtil::delVar('formiculaCaptcha');

        // Check hooked modules for validation
        $hookvalidators = $this->notifyHooks(new Zikula_ValidationHook('formicula.ui_hooks.forms.validate_edit', new Zikula_Hook_ValidationProviders()))->getValidators();
        if ($hookvalidators->hasErrors()) {
            SessionUtil::setVar('formicula_userData', serialize($userData));
            SessionUtil::setVar('formicula_customFields', serialize($customFields));

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

        if ($ownContactsUse != -1 && SessionUtil::getVar('formiculaOwnContacts', null) != null) {
            $sessionContacts = SessionUtil::getVar('formiculaOwnContacts');
            $contacts = $sessionContacts[$ownContactsUse];
            if (!ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'checkOwncontacts', ['ownContacts' => $contacts])) {
                return $this->redirect($errorReturnUrl); 
            }
            $contact = $contacts[$cid-1];
            $ownContacts = true;
        } else {
            $ownContacts = false;
            $contact = ModUtil::apiFunc('Formicula', 'user', 'getContact', [
                'cid'  => $cid,
                'form' => $form
            ]);
        }

        if (true === $ownContacts) {
            if (!SecurityUtil::checkPermission('Formicula::Owncontacts', "$form::", ACCESS_COMMENT)) {
                return LogUtil::registerPermissionError($errorReturnUrl);
            }
        } else {
            if (!SecurityUtil::checkPermission('Formicula::', "$form:$cid:", ACCESS_COMMENT)) {
                return LogUtil::registerPermissionError($errorReturnUrl);
            }
        }

        $this->view->setCaching(false);
        $this->view->assign('contact', $contact)
                   ->assign('userData', $userData)
                   ->assign('userFormat', $userFormat)
                   ->assign('adminFormat', $adminFormat);

        if (ModUtil::apiFunc('Formicula', 'user', 'checkArguments', [
            'userData'     => $userData,
            'customFields' => $customFields,
            'userFormat'   => $userFormat
        ]) == true) {

            $userDataComment = $userData['comment'];

            if ($adminFormat == 'plain') {
                // remove tags from comment to avoid spam
                $userData['comment'] = strip_tags($userDataComment);
            }
            // send the submitted data to the contact(s)
            if (ModUtil::apiFunc('Formicula', 'user', 'sendtoContact', [
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
                $this->view->assign('sentToUser', ModUtil::apiFunc('Formicula', 'user', 'sendtoUser', [
                    'contact'      => $contact,
                    'userData'     => $userData,
                    'customFields' => $customFields,
                    'form'         => $form,
                    'format'       => $userFormat
                ]));
            }

            // store the submitted data in the database
            $store_data = $this->getVar('store_data');
            if ($store_data == 1 && false === $ownContacts) {
                $store_data_forms = $this->getVar('store_data_forms');
                $store_data_forms_arr = explode(',', $store_data_forms);
                if (empty($store_data_forms) || (is_array($store_data_forms_arr) && in_array($form, $store_data_forms_arr))) {
                    ModUtil::apiFunc('Formicula', 'user', 'storeInDatabase', [
                        'contact'      => $contact,
                        'userData'     => $userData,
                        'customFields' => $customFields,
                        'form'         => $form
                    ]);
                }
            }

            $this->view->assign('customFields', ModUtil::apiFunc('Formicula', 'user', 'removeUploadInformation', array('customFields' => $customFields)));

            return $this->view->fetch('forms' . DIRECTORY_SEPARATOR . $form."_userconfirm.tpl");
        } else {
            $this->view->assign('customFields', ModUtil::apiFunc('Formicula', 'user', 'removeUploadInformation', array('customFields' => $customFields)));

            return $this->view->fetch('forms' . DIRECTORY_SEPARATOR . $form."_usererror.tpl");
        }
    }
}
