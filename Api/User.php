<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Formicula_Api_User extends Zikula_AbstractApi
{
    /**
     * readValidContacts
     * reads the contact list and returns it as array.
     * This function filters out the entries the user is not allowed to see
     *
     * @param form int form id
     * @return array with contact information
     */
    public function readValidContacts($args)
    {
        $allContacts = ModUtil::apiFunc('ZikulaFormiculaModule', 'admin', 'readContacts');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if (false === $allContacts) {
            return LogUtil::registerError($this->__('Error! No contacts defined.'));
        }

        // Put items into result array.  Note that each item is checked
        // individually to ensure that the user is allowed access to it before it
        // is added to the results array
        $visibleContacts = [];
        foreach($allContacts as $contact) {
            if (SecurityUtil::checkPermission('ZikulaFormiculaModule::', $args['form'] . ':.*:', ACCESS_COMMENT)) {
                $visibleContacts[] = $contact;
            }
        }

        // Return the contacts
        return $visibleContacts;
    }

    /**
     * sendtoContact
     * sends the mail to the contact
     *
     * @param userData array with user submitted data
     * @param contact array of contact information (single contact)
     * @param customFields array of custom fields information
     * @param form int form id
     * @param format   string email format, either 'plain' or 'html'
     * @return boolean
     */
    public function sendtoContact($args)
    {
        $userData = $args['userData'];
        $contact = $args['contact'];
        $customFields = $args['customFields'];
        $form = DataUtil::formatForOS($args['form']);
        $format = $args['format'];

        $mailerModule = 'ZikulaMailerModule';
        if (!ModUtil::available($mailerModule)) {
            // no mailer module - error!
            return false;
        }

        $sitename = System::getVar('sitename');
        $ipAddress = getenv('REMOTE_ADDR');

        $view = Zikula_View::getInstance('ZikulaFormiculaModule', false, null, true);
        $templateParameters = [
             'hostName' => gethostbyaddr($ipAddress),
             'ipAddress' => $ipAddress,
             'form' => $form,
             'contact' => $contact,
             'userdata' => $userData,
             'siteName' => $sitename
        ];
        $view->assign($templateParameters);

        // attach all files we have got
        $attachments = [];
        $uploadDirectory = dirname(ZLOADER_PATH) . '/' . ModUtil::getVar('ZikulaFormiculaModule', 'uploadDirectory');

        foreach ($customFields as $k => $customField) {
            if (!isset($customField['data']) || !is_array($customField['data']))  {
                continue;
            }

            if ($customField['data']['name']) {
                $attachments[] = $uploadDirectory . '/' . $customField['data']['name'];
            }
            $customFields[$k]['data'] = $customField['data']['name'];
        }
        $view->assign('customFields', $customFields);

        switch ($format) {
            case 'html' :
                $body = $view->fetch('Form/' . $form . '/adminMail.html.twig');
                $html = true;
                break;
            default:
                $body = $view->fetch('Form/' . $form . '/adminMail.txt.twig');
                $html = false;
        }

        // subject of the emails can be determined from the form
        $subject = !empty($userData['adminsubject']) ? $userData['adminsubject'] : $sitename." - ".$contact['name'];

        $mailArgs = [
            'fromname'    => $userData['uname'],
            'toname'      => $contact['name'],
            'toaddress'   => $contact['email'],
            'subject'     => $subject,
            'body'        => $body,
            'attachments' => $attachments,
            'html'        => $html
        ];

        if (true === ModUtil::getVar('ZikulaFormiculaModule', 'useContactsAsSender', true)) {
            $mailArgs['fromaddress'] = $userData['uemail'];
        }

        $result = ModUtil::apiFunc($mailerModule, 'user', 'sendmessage', $mailArgs);

        if (true === ModUtil::getVar('ZikulaFormiculaModule', 'deleteUploadedFiles', true)) {
            foreach ($attachments as $attachment) {
                if (file_exists($attachment) && is_file($attachment)) {
                    unlink($attachment);
                }
            }
        }

        return $result;
    }

    /**
     * sendtoUser
     * sends the confirmation mail to the user
     *
     * @param userData array with user submitted data
     * @param contact  array with contact data
     * @param customFields   array of custom fields information
     * @param form     int form id
     * @param format   string email format, either 'plain' or 'html'
     * @return boolean
     */
    public function sendtoUser($args)
    {
        $userData = $args['userData'];
        $contact = $args['contact'];
        $customFields = $args['customFields'];
        $form = DataUtil::formatForOS($args['form']);
        $format = $args['format'];

        $mailerModule = 'ZikulaMailerModule';
        if (!ModUtil::available($mailerModule)) {
            // no mailer module - error!
            return false;
        }

        $sitename = System::getVar('sitename');
        $ipAddress = getenv('REMOTE_ADDR');

        $view = Zikula_View::getInstance('ZikulaFormiculaModule', false, null, true);
        $view->assign('hostName', gethostbyaddr($ipAddress))
             ->assign('ipAddress', $ipAddress)
             ->assign('form', $form)
             ->assign('contact', $contact)
             ->assign('userdata', $userData)
             ->assign('siteName', $sitename)
             ->assign('customFields', ModUtil::apiFunc('ZikulaFormiculaModule', 'user', 'removeUploadInformation', ['customFields' => $customFields]));

        switch ($format) {
            case 'html' :
                $body = $view->fetch('Form/' . $form . '/userMail.html.twig');
                $html = true;
                break;
            default:
                $body = $view->fetch('Form/' . $form . '/userMail.txt.twig');
                $html = false;
        }

        // check for sender name
        if (!empty($contact['sname'])) {
            $fromname = $contact['sname'];
        } else {
            $fromname = $sitename . ' - ' . DataUtil::formatForDisplay($this->__('Contact form'));
        }
        // check for sender email
        if (!empty($contact['semail'])) {
            $frommail = $contact['semail'];
        } else {
            $frommail = $contact['email'];
        }

        // check for subject, can be in the form or in the contact
        if (!empty($contact['ssubject']) || !empty($userData['usersubject'])) {
            $subject = !empty($userData['usersubject']) ? $userData['usersubject'] : $contact['ssubject'];
            // replace some placeholders
            // %s = sitename
            // %l = slogan
            // %u = site url
            // %c = contact name
            // %n<num> = user defined field name <num>
            // %d<num> = user defined field data <num>
            $subject = str_replace('%s', DataUtil::formatForDisplay($sitename), $subject);
            $subject = str_replace('%l', DataUtil::formatForDisplay(System::getVar('slogan')), $subject);
            $subject = str_replace('%u', System::getBaseUrl(), $subject);
            $subject = str_replace('%c', DataUtil::formatForDisplay($contact['sname']), $subject);
            foreach ($customFields as $num => $customField) {
                $subject = str_replace('%n' . $num, $customField['name'], $subject);
                $subject = str_replace('%d' . $num, $customField['data'], $subject);
            }
        } else {
            $subject = $sitename . ' - ' . $contact['name'];
        }

        $mailArgs = [
            'fromname'  => $fromname,
            'toname'    => $userData['uname'],
            'toaddress' => $userData['uemail'],
            'subject'   => $subject,
            'body'      => $body,
            'html'      => $html
        ];

        if (true === ModUtil::getVar('ZikulaFormiculaModule', 'useContactsAsSender', true)) {
            $mailArgs['fromaddress'] = $frommail;
        }

        return ModUtil::apiFunc($mailerModule, 'user', 'sendmessage', $mailArgs);
    }

    /**
     * storeInDatabase
     * stores a form submit in the database
     *
     * @param userData array with user submitted data
     * @param contact  array with contact data
     * @param customFields   array of custom fields information
     * @param form     int form id
     * @return boolean
     */
    public function storeInDatabase($args)
    {
        $userData = $args['userData'];
        $contact  = $args['contact'];
        $customFields = $args['customFields'];
        $form = DataUtil::formatForOS($args['form']);

        $formsubmit['form'] = $form;
        $formsubmit['cid'] = $contact['cid'];
        $formsubmit['name'] = $userData['uname'];
        $formsubmit['email'] = $userData['uemail'];
        $formsubmit['phone'] = $userData['phone'];
        $formsubmit['company'] = $userData['company'];
        $formsubmit['url'] = $userData['url'];
        $formsubmit['location'] = $userData['location'];
        $formsubmit['comment'] = $userData['comment'];
        $customArray = [];
        foreach ($customFields as $customField) {
            $customArray[$customField['name']] = $customField['data'];
        }
        $formsubmit['customData'] = $customArray;
        $ipAddress = getenv('REMOTE_ADDR');
        $formsubmit['ipAddress'] = $ipAddress;
        $formsubmit['hostName'] = gethostbyaddr($ipAddress);

        if (!($obj = DBUtil::insertObject($formsubmit, 'formsubmits', 'sid'))) {
            return LogUtil::registerError($this->__f('Error! Could not store data submitted by form %s.', ['%s' => $form]));
        }

        return true;
    }

    /**
     * checkArguments
     * checks if mandatory arguments are correct
     *
     * @param userData array with user submitted data, we are interested in uemail, uname and comment here
     * @param customFields array with custom data
     * @param userformat string format of users email for relaxed checking if userformat=none
     * @return boolean
     */
    public function checkArguments($args)
    {
        $userData = $args['userData'];
        $customFields = $args['customFields'];
        $userformat = $args['userformat'];

        $ok = true;

        if ($userformat != 'none') {
            if (!isset($userData['uemail']) || (System::varValidate($userData['uemail'], 'email') == false)) {
                $ok = LogUtil::registerError($this->__('Error! No or incorrect email address supplied.'));
            }

            if (!isset($userData['uname']) || empty($userData['uname'])) {
                $ok = LogUtil::registerError($this->__('Error! No or invalid username given.'));
            }
        }

        foreach ($customFields as $field) {
            if (isset($field['mandatory']) && $field['mandatory']) {
                if (!is_array($field['data']) && (empty($field['data']))) {
                    $ok = LogUtil::registerError($this->__('Error! Mandatory field:' . DataUtil::formatForDisplay($field['name'])));
                }
                if (($field['upload'] == true) && ($field['data']['size'] == 0)) {
                    $ok = LogUtil::registerError($this->__('Error! Upload error.'));
                }
            }
        }

        return $ok;
    }

    /**
     * removeUploadInformation
     * replaces the information about uploaded files with the filename so that we can use it in the
     * templates
     *
     * @param customFields array of custom fields
     * @return cleaned custom array
     */
    public function removeUploadInformation($args)
    {
        $customFields = [];
        if (!isset($args['customFields']) || !is_array($args['customFields'])) {
            return $customFields;
        }

        $customFields = $args['customFields'];
        foreach ($customFields as $k => $customField) {
            if (isset($customField['upload']) && $customField['upload'] == true) {
                $customFields[$k]['data'] = $customFields[$k]['data']['name'];
            }
        }

        return $customFields;
    }
}
