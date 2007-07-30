<?php
// $Id$
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Frank Schummertz
// Purpose of file:  formicula user functions
// ----------------------------------------------------------------------

/**
 * main
 * main entry point for the user
 *
 *@param form int number of form to show
 *@returns pnRender output
 */
function formicula_user_main($args=array())
{
    $form = (int)FormUtil::getPassedValue('form', (isset($args['form'])) ? $args['form'] : 0, 'GETPOST');
    $cid  = (int)FormUtil::getPassedValue('cid',  (isset($args['cid'])) ? $args['cid'] : -1,  'GETPOST');

    // get subitted information - will be passed to the template
    // addinfo is an array:
    // addinfo[name1] = value1
    // addinfo[name2] = value2
    $addinfo  = FormUtil::getPassedValue('addinfo',  (isset($args['addinfo'])) ? $args['addinfo'] : array(),  'GETPOST');

    // reset captcha
    SessionUtil::delVar('formicula_captcha');

    if ($cid == -1) {
        $contacts = pnModAPIFunc('formicula',
                                 'user',
                                 'readValidContacts',
                                 array('form' => $form));
    } else {
        $contacts[] = pnModAPIFunc('formicula',
                                   'user',
                                   'getContact',
                                   array('cid'  => $cid,
                            	         'form' => $form));
    }

    if (count($contacts) == 0) {
        return LogUtil::registerPermissionError('index.php');
    }

    if (pnUserLoggedIn()) {
        $uname = (pnUserGetVar('name') == '') ? pnUserGetVar('uname') : pnUserGetVar('name');
        $uemail = pnUserGetVar('email');
    } else {
        $uname = '';
        $email = '';
    }

    $spamcheck = pnModGetVar('formicula', 'spamcheck');
    if($spamcheck == 1) {
        $excludespamcheck = explode(',', pnModGetVar('formicula', 'excludespamcheck'));
        if(is_array($excludespamcheck) && array_key_exists($form, array_flip($excludespamcheck))) {
            $spamcheck = 0;
        }
    }

    $pnr = pnRender::getInstance('formicula', false);
    $pnr->add_core_data();
    $pnr->assign('uname', $uname);
    $pnr->assign('uemail', $uemail);
    $pnr->assign('contacts', $contacts);
    $pnr->assign('addinfo', $addinfo);
    $pnr->assign('spamcheck', $spamcheck);
    return $pnr->fetch($form.'_userform.html');
}

/**
 * send
 * sends the mail to the contact and, if configured, to the user
 *@param cid         int contact id
 *@param form        int form id
 *@param userformat  string email format for user, either 'plain' (default) or 'html'
 *@param adminformat string email format for admin, either 'plain' (default) or 'html'
 *@param uname       string users name
 *@param uemail      string users email
 *@param url         string users homepage
 *@param phone       string users phone
 *@param company     string users company
 *@param location    string users location
 *@param comment     string users comment
 *@returns pnRender output
 */
function formicula_user_send($args=array())
{
    global $_FILES;

    $form           = (int)FormUtil::getPassedValue('form',        (isset($args['form'])) ? $args['form'] : 0, 'GETPOST');
    $cid            = (int)FormUtil::getPassedValue('cid',         (isset($args['cid'])) ? $args['cid'] : 0,  'GETPOST');
    $captcha        = (int)FormUtil::getPassedValue('captcha',     (isset($args['captcha'])) ? $args['captcha'] : 0, 'GETPOST');
    $userformat     =      FormUtil::getPassedValue('userformat',  (isset($args['userformat'])) ? $args['userformat'] : 'plain',  'GETPOST');
    $adminformat    =      FormUtil::getPassedValue('adminformat', (isset($args['adminformat'])) ? $args['adminformat'] : 'plain', 'GETPOST');
    $numfields      = (int)FormUtil::getPassedValue('numfields',   (isset($args['numfields'])) ? $args['numfields'] : 0,  'GETPOST');
    $returntourl    =      FormUtil::getPassedValue('returntourl', (isset($args['returntourl'])) ? $args['returntourl'] : '',  'GETPOST');
    $ud['uname']    =      FormUtil::getPassedValue('uname',       (isset($args['uname'])) ? $args['uname'] : '', 'GETPOST');
    $ud['uemail']   =      FormUtil::getPassedValue('uemail',      (isset($args['uemail'])) ? $args['uemail'] : '',  'GETPOST');
    $ud['url']      =      FormUtil::getPassedValue('url',         (isset($args['url'])) ? $args['url'] : '', 'GETPOST');
    $ud['phone']    =      FormUtil::getPassedValue('phone',       (isset($args['phone'])) ? $args['phone'] : '',  'GETPOST');
    $ud['company']  =      FormUtil::getPassedValue('company',     (isset($args['company'])) ? $args['company'] : '', 'GETPOST');
    $ud['location'] =      FormUtil::getPassedValue('location',    (isset($args['location'])) ? $args['location'] : '',  'GETPOST');
    $ud['comment']  =      FormUtil::getPassedValue('comment',     (isset($args['comment'])) ? $args['comment'] : '', 'GETPOST');

    if(empty($cid) && empty($form)) {
        return pnRedirect('index.php');
    }

    // remove tags from comment to avoid spam
    $comments = strip_tags($comments);

    // check captcha
    $spamcheck = pnModGetVar('formicula', 'spamcheck');
    if($spamcheck == 1) {
        $excludespamcheck = explode(',', pnModGetVar('formicula', 'excludespamcheck'));
        if(is_array($excludespamcheck) && array_key_exists($form, array_flip($excludespamcheck))) {
            $spamcheck = 0;
        }
    }
    if($spamcheck==1) {
        $captcha_ok = false;
        $cdata = @unserialize(SessionUtil::getVar('formicula_captcha'));
        if(is_array($cdata)) {
            switch($cdata['z']) {
                case '0':
                    $captcha_ok = (((int)$cdata['x'] + (int)$cdata['y'])== $captcha);
                    break;
                case '1':
                    $captcha_ok = (((int)$cdata['x'] - (int)$cdata['y'])== $captcha);
                    break;
                case '2':
                    $captcha_ok = (((int)$cdata['x'] * (int)$cdata['y'])== $captcha);
                    break;
                default:
                    // $captcha_ok is false
            }
        }

        if($captcha_ok==false) {
            SessionUtil::delVar('formicula_captcha');
            // todo: append params to $returntourl and redirect
            $params = array('form' => $form);
            if(is_array($addinfo) && count($addinfo)>0) {
                $params['addinfo'] = $addinfo;
            }
            return LogUtil::registerError(_FOR_WRONGCAPTCHA, null, pnServerGetVar('HTTP_REFERER'));
        }
    }
    SessionUtil::delVar('formicula_captcha');

    if(!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('formicula', 'user', 'main', array('form' => $form)));
    }
    
    if(empty($userformat) || ($userformat<>'plain' && $userformat<>'html' && $userformat<>'none')) {
        $userformat = 'plain';
    }
    if(empty($adminformat) || ($adminformat<>'plain' && $adminformat<>'html')) {
        $adminformat = 'plain';
    }

    if($userformat == 'none') {
        // fake values for checkargument function
        $ud['uemail'] = pnConfigGetVar('adminmail');
        $ud['uname'] =  pnConfigGetVar('adminmail');
    }

    if(!SecurityUtil::checkPermission('formicula::', "$form:$cid:", ACCESS_COMMENT)) {
        return LogUtil::registerPermissionError(pnModURL('formicula', 'user', 'main', array('form' => $form)));
    }

    // very basic input validation against HTTP response splitting
    $ud['uemail'] = str_replace(array('\r', '\n', '%0d', '%0a'), '', $ud['uemail']);

    // addon: custom fields
    $uploaddir = pnModGetVar('formicula', 'upload_dir');
    // check if it ends with / or we add one
    if(substr($uploaddir, strlen($uploaddir)-1, 1) <> "/") {
        $uploaddir .= "/";
    }
    $custom = array();
    for($i=0;$i<$numfields;$i++) {
        $custom[$i]['name'] = FormUtil::getPassedValue('custom'.$i.'name');
        $custom[$i]['mandatory'] = (FormUtil::getPassedValue('custom'.$i.'mandatory') == 1) ? true : false;

        if(isset($_FILES['custom'.$i.'data']['tmp_name'])) {
            $custom[$i]['data']['error'] = $_FILES['custom'.$i.'data']['error'];
            if($custom[$i]['data']['error'] == 0) {
                $custom[$i]['data']['size']     = $_FILES['custom'.$i.'data']['size'];
                $custom[$i]['data']['type']     = $_FILES['custom'.$i.'data']['type'];
                $custom[$i]['data']['name']     = $_FILES['custom'.$i.'data']['name'];
                $custom[$i]['upload'] = true;
                move_uploaded_file($_FILES['custom'.$i.'data']['tmp_name'], DataUtil::formatForOS($uploaddir.$custom[$i]['data']['name']));
            } else {
                // error - replace the 'data' with an errormessage
                $custom[$i]['data'] = constant("_FOR_UPLOADERROR".$custom[$i]['data']['error']);
            }
        } else {
            $custom[$i]['data'] = FormUtil::getPassedValue('custom'.$i.'data');
            $custom[$i]['upload'] = false;
        }
    }

    $contact = pnModAPIFunc('formicula', 'user', 'getContact',
                            array('cid'  => $cid,
                                  'form' => $form));

    $pnr = pnRender::getInstance('formicula', false);
    $pnr->assign('contact', $contact);
    $pnr->assign('userdata', $ud);

    if(pnModAPIFunc('formicula',
                     'user',
                     'checkArguments',
                     array('userdata'   => $ud,
                           'custom'     => $custom)) == true) {
        if(pnModAPIFunc('formicula',
                         'user',
                         'sendtoContact',
                         array('contact'  => $contact,
                               'userdata' => $ud,
                               'custom'   => $custom,
                               'form'     => $form,
                               'format'   => $adminformat)) == false) {
            return LogUtil::registerError(_FOR_ERRORSENDINGMAIL, null, pnModURL('formicula', 'user', 'main', array('form' => $form)));
        }

        if((pnModGetVar('formicula', 'send_user') == 1) && ($userformat <> 'none')) {
            // we replace the array of data of uploaded files with the filename
            $pnr->assign('sendtouser', pnModAPIFunc('formicula',
                                                     'user',
                                                     'sendtoUser',
                                                     array('contact'  => $contact,
                                                           'userdata' => $ud,
                                                           'custom'   => $custom,
                                                           'form'     => $form,
                                                           'format'   => $userformat )));
        }

        $pnr->assign('custom', pnModAPIFunc('formicula', 'user', 'removeUploadInformation', array('custom' => $custom)));
        return $pnr->fetch($form."_userconfirm.html");
    } else {
        $pnr->assign('custom', pnModAPIFunc('formicula', 'user', 'removeUploadInformation', array('custom' => $custom)));
        return $pnr->fetch($form."_usererror.html");
    }
}

?>