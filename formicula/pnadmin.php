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

/**
 * main
 * main entry point for configuration
 *
 *@param none
 *@returns pnRender output
 */
function Formicula_admin_main()
{
    return pnModFunc('formicula', 'admin', 'view');
}

/**
 * edit
 * editing existing and adding new contacts
 *
 *@param cid int contact id, -1 for new contacts
 *@returns pnRender output
 */
function Formicula_admin_edit()
{
    if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADD)) {
        return LogUtil::registerPermissionError('index.php');
    }

    // check necessary environment
    formicula_envcheck();

    $cid = FormUtil::getPassedValue('cid', -1, 'GET');

    $pnr = new pnRender('formicula');
    $pnr->caching = false;

    if((isset($cid)) && ($cid<>-1)) {
        if (!SecurityUtil::checkPermission('formicula::', "::", ACCESS_EDIT)) {
            return LogUtil::registerPermissionError(pnModURL('formicula', 'admin', 'main'));
        }

        $contact = pnModAPIFunc('formicula',
                             'admin',
                             'getContact',
                             array('cid' => $cid));
        if ($contact == false) {
            return LogUtil::registerError(_FOR_NOSUCHCONTACT, null, pnModURL('formicula', 'admin', 'main'));
        }

        $pnr->assign('contact', $contact);
        $pnr->assign('mode', 'update');
    } else  {
        $pnr->assign('mode', 'create');
    }
    return $pnr->fetch('adminedit.html');
}

/**
 * create
 * add new contact to the database
 *
 *@param name string contact name
 *@param email string contact email
 *@returns pnRender output on error or forwards to view()
 */
function formicula_admin_create()
{
    if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADD)) {
        return LogUtil::registerPermissionError('index.php');
    }

    // check necessary environment
    formicula_envcheck();

    $name     =      FormUtil::getPassedValue('cname', '', 'POST');
    $email    =      FormUtil::getPassedValue('email', '', 'POST');
    $public   = (int)FormUtil::getPassedValue('public', 0, 'POST');
    $sname    =      FormUtil::getPassedValue('sname', '', 'POST');
    $semail   =      FormUtil::getPassedValue('semail', '', 'POST');
    $ssubject =      FormUtil::getPassedValue('ssubject', '', 'POST');

    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('formicula', 'admin', 'main'));
    }

    if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADD)) {
        return LogUtil::registerPermissionError(pnModURL('formicula', 'admin', 'main'));
    }

    if(empty($email) || !pnVarValidate($email, 'email')) {
        return LogUtil::registerError(_FOR_ILLEGALEMAIL . ': ' . $email, null, pnModURL('formicula', 'admin', 'main'));
    }
    if(!empty($semail) && !pnVarValidate($semail, 'email')) {
        return LogUtil::registerError(_FOR_ILLEGALEMAIL . ': ' . $semail, null, pnModURL('formicula', 'admin', 'main'));
    }

    $res = pnModAPIFunc('formicula',
                        'admin',
                        'createContact',
                        array('name'     => $name,
                              'email'    => $email,
                              'public'   => $public,
                              'sname'    => $sname,
                              'semail'   => $semail,
                              'ssubject' => $ssubject));

    if ($res != false) {
        LogUtil::registerStatus(_FOR_CONTACTCREATED);
    } else {
        LogUtil::registerStatus(_FOR_ERRORCREATINGCONTACT);
    }

    return pnRedirect(pnModURL('formicula', 'admin', 'view'));
}

/**
 * update
 * updates an existing contact in the database
 *
 *@param cid int contact id
 *@param name string contact name
 *@param email string contact email
 *@returns pnRender output on error or forwards to view()
 */
function formicula_admin_update()
{
    if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError('index.php');
    }

    $cid      = (int)FormUtil::getPassedValue('cid', -1, 'POST');
    $name     =      FormUtil::getPassedValue('cname', '', 'POST');
    $email    =      FormUtil::getPassedValue('email', '', 'POST');
    $public   = (int)FormUtil::getPassedValue('public', 0, 'POST');
    $sname    =      FormUtil::getPassedValue('sname', '', 'POST');
    $semail   =      FormUtil::getPassedValue('semail', '', 'POST');
    $ssubject =      FormUtil::getPassedValue('ssubject', '', 'POST');

    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('formicula', 'admin', 'main'));
    }

    if(empty($email) || !pnVarValidate($email, 'email')) {
        return LogUtil::registerError(_FOR_ILLEGALEMAIL . ': ' . $email, null, pnModURL('formicula', 'admin', 'main'));
    }
    if(!empty($semail) && !pnVarValidate($semail, 'email')) {
        return LogUtil::registerError(_FOR_ILLEGALEMAIL . ': ' . $semail, null, pnModURL('formicula', 'admin', 'main'));
    }

    if(pnModAPIFunc('formicula',
                     'admin',
                     'updateContact',
                     array('cid'      => $cid,
                           'name'     => $name,
                           'email'    => $email,
                           'public'   => $public,
                           'sname'    => $sname,
                           'semail'   => $semail,
                           'ssubject' => $ssubject))) {
        // Success
        LogUtil::registerStatus(_FOR_CONTACTUPDATED);
    }
    return pnRedirect(pnModURL('formicula', 'admin', 'view'));
}

/**
 * delete
 * deletes an existing contact from the database
 * When called for the first time its produces an "Are you sure?" page. If the admin
 * clicks on OK, confirmation is set and the function deletes the entry
 *
 *@param cid int contact id
 *@param confirmation string any value
 *@returns pnRender output on error or forwards to view()
 */
function formicula_admin_delete()
{
    if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_DELETE)) {
        return LogUtil::registerPermissionError('index.php');
    }

    // check necessary environment
    formicula_envcheck();

    $cid          = (int)FormUtil::getPassedValue('cid', -1, 'POST');
    $confirmation =      FormUtil::getPassedValue('confirmation', '', 'POST');

    $contact = pnModAPIFunc('formicula',
                            'admin',
                            'getContact',
                            array('cid' => $cid));

    if ($contact == false) {
        return LogUtil::registerError(_FOR_NOSUCHCONTACT, null, pnModURL('formicula', 'admin', 'main'));
    }

    // Check for confirmation.
    if (empty($confirmation)) {
        $pnr = new pnRender('formicula');
        $pnr->caching = false;
        $contact['cid'] = $cid;
        $pnr->assign('contact', $contact);
        return $pnr->fetch('admindelete.html');
    }

    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('formicula', 'admin', 'main'));
    }

    if (pnModAPIFunc('formicula',
                     'admin',
                     'deleteContact',
                     array('cid' => $cid))) {
        // Success
        LogUtil::registerStatus(_FOR_CONTACTDELETED);
    }

    return pnRedirect(pnModURL('formicula', 'admin', 'view'));
}

/**
 * view
 * show list of contacts
 *
 *@param none
 *@returns pnRender output
 */
function formicula_admin_view()
{
    if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError('index.php');
    }

    // check necessary environment
    formicula_envcheck();
    
    $pnr = new pnRender('formicula', false);

    // read all items
    $allcontacts = pnModAPIFunc('formicula',
                                'admin',
                                'readContacts');
    // only use those where we have the necessary rights for
    $allowedcontacts = array();
    foreach ($allcontacts as $contact) {
        $cid = $contact['cid'];
        if (SecurityUtil::checkPermission('formicula::', ":$cid:", ACCESS_EDIT)) {
            $allowedcontact = array('cid'        => $contact['cid'],
                                    'name'       => $contact['name'],
                                    'email'      => $contact['email'],
                                    'public'     => $contact['public'],
                                    'sname'      => $contact['sname'],
                                    'semail'     => $contact['semail'],
                                    'ssubject'   => $contact['ssubject'],
                                    'acc_edit'   => true,
                                    'acc_delete' => false);

            if (SecurityUtil::checkPermission('formicula::', ":$cid:", ACCESS_DELETE)) {
                $allowedcontact['acc_delete'] = true;
            }
            array_push($allowedcontacts, $allowedcontact);
        }
    }
    $pnr->assign('contacts', $allowedcontacts);
    return $pnr->fetch('adminview.html');
}

/**
 * modifyconfig
 * main entry point for configuration of module behaviour
 *
 *@param none
 *@returns pnRender output
 */
function formicula_admin_modifyconfig()
{
    if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError('index.php');
    }

    // check necessary environment
    formicula_envcheck();

    $pnr = new pnRender('formicula', false);
    $pnr->add_core_data();
    $pnr->assign('upload_dir_writable', is_writable(pnModGetVar('formicula', 'upload_dir')));
    return $pnr->fetch('adminconfig.html');
}

/**
 * updateconfig
 * saves the updated module configuration
 *
 *@param show_phone        int 1=show phone entry field, 0=do not show
 *@param show_company      int 1=show company entry field, 0=do not show
 *@param show_url          int 1=show url entry field, 0=do not show
 *@param show_location     int 1=show location entry field, 0=do not show
 *@param show_comment      int 1=show comment textarea, 0=do not show
 *@param send_user         int 1=send cofirmation mail to user, 0=do not send cofirmation mail
 *@param delete_file       int 1=delete attachments after sending, 0=do not delete
 *@param upload_dir        string folder to store uploaded files
 *@param spamcheck         int 1=activate simple captcha
 *@param excludespamcheck  string comma separated list of ids where spam check is not used
 *@returns nothing, but forwards to view()
 */
function formicula_admin_updateconfig($args)
{
    if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError('index.php');
    }

    $show_phone =        FormUtil::getPassedValue('show_phone', '', 'POST');
    $show_company =      FormUtil::getPassedValue('show_company', '', 'POST');
    $show_url =          FormUtil::getPassedValue('show_url', '', 'POST');
    $show_location =     FormUtil::getPassedValue('show_location', '', 'POST');
    $show_comment =      FormUtil::getPassedValue('show_comment', '', 'POST');
    $send_user =         FormUtil::getPassedValue('send_user', '', 'POST');
    $delete_file =       FormUtil::getPassedValue('delete_file', '', 'POST');
    $upload_dir =        FormUtil::getPassedValue('upload_dir', '', 'POST');
    $spamcheck =         FormUtil::getPassedValue('spamcheck', '', 'POST');
    $excludespamcheck =  FormUtil::getPassedValue('excludespamcheck', '', 'POST');

    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('formicula', 'admin', 'main'));
    }

    pnModSetVar('formicula', 'show_phone', (empty($show_phone)) ? 0 : (int)$show_phone );
    pnModSetVar('formicula', 'show_company', (empty($show_company)) ? 0 : (int)$show_company);
    pnModSetVar('formicula', 'show_url', (empty($show_url)) ? 0 : (int)$show_url);
    pnModSetVar('formicula', 'show_location', (empty($show_location)) ? 0 : (int)$show_location);
    pnModSetVar('formicula', 'show_comment', (empty($show_comment)) ? 0 : (int)$show_comment);
    pnModSetVar('formicula', 'send_user', (empty($send_user)) ? 0 : (int)$send_user);
    pnModSetVar('formicula', 'delete_file', (empty($delete_file)) ? 0 : (int)$delete_file);
    pnModSetVar('formicula', 'upload_dir',   $upload_dir);
    pnModSetVar('formicula', 'spamcheck', (empty($spamcheck)) ? 0 : (int)$spamcheck);
    pnModSetVar('formicula', 'excludespamcheck', $excludespamcheck);

    return pnRedirect(pnModURL('formicula', 'admin', 'modifyconfig'));
}

function formicula_envcheck()
{
    if(!pnModAvailable('Mailer')) {
        LogUtil::registerError(_FOR_NOMAILERMODULE);
    }

    if(pnModGetVar('formicula', 'spamcheck') <> 0) {
        $freetype = function_exists('imagettfbbox');
        if(!$freetype || ( !(imagetypes() && IMG_PNG)
                      && !(imagetypes() && IMG_JPG)
                      && !(imagetypes() && IMG_GIF)) ) {
            LogUtil::registerStatus(_FOR_NOIMAGEFUNCTION);
            pnModSetVar('formicula', 'spamcheck', 0);
        }
        
        $cachedir = pnConfigGetVar('temp') . '/formicula_cache';
        if(!file_exists($cachedir) || !is_writable($cachedir)) {
            LogUtil::registerStatus(_FOR_CACHEDIRPROBLEM);
            pnModSetVar('formicula', 'spamcheck', 0);
        }
        if(!file_exists($cachedir.'/.htaccess')) {
            LogUtil::registerStatus(_FOR_HTACCESSPROBLEM);
            pnModSetVar('formicula', 'spamcheck', 0);
        }
    }
    return true;
}

?>