<?php
// $Id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------

include_once("modules/formicula/common.php");

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
    $cid = pnVarCleanFromInput('cid');

    if (!pnSecAuthAction(0, 'formicula::', '::', ACCESS_ADD)) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_NOAUTH));
    }

    $pnr =& new pnRender('formicula');
    $pnr->caching = false;

    if((isset($cid)) && ($cid<>-1)) {
        if (!pnSecAuthAction(0, 'formicula::', "::", ACCESS_EDIT)) {
            return showErrorMessage(pnVarPrepForDisplay(_FOR_NOAUTH));
        }

        $contact = pnModAPIFunc('formicula',
                             'admin',
                             'getContact',
                             array('cid' => $cid));
        if ($contact == false) {
            return showErrorMessage(pnVarPrepForDisplay(_FOR_NOSUCHCONTACT));
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
function formicula_admin_create($args)
{

    list($name,
         $email,
         $public,
         $sname,
         $semail,
         $ssubject) = pnVarCleanFromInput('cname',
                                          'email',
                                          'public',
                                          'sname',
                                          'semail',
                                          'ssubject');

    extract($args);

    if (!pnSecConfirmAuthKey()) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_BADAUTHKEY));
    }

    if (!pnSecAuthAction(0, 'formicula::', '::', ACCESS_ADD)) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_NOAUTH));
    }

    if(!empty($email) && !pnVarValidate($email, 'email')) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_ILLEGALEMAIL) . ': ' . pnVarPrepHTMLDisplay($email));
    }
    if(!empty($semail) && !pnVarValidate($semail, 'email')) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_ILLEGALEMAIL) . ': ' . pnVarPrepHTMLDisplay($semail));
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
        pnSessionSetVar('statusmsg', _FOR_CONTACTCREATED);
    } else {
        pnSessionSetVar('statusmsg', _FOR_ERRORCREATINGCONTACT);
    }

    pnRedirect(pnModURL('formicula', 'admin', 'view'));
    return true;
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
function formicula_admin_update($args)
{
    list($cid,
         $name,
         $email,
         $public,
         $sname,
         $semail,
         $ssubject) = pnVarCleanFromInput('cid',
                                          'cname',
                                          'email',
                                          'public',
                                          'sname',
                                          'semail',
                                          'ssubject');

    extract($args);

    if (!pnSecConfirmAuthKey()) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_BADAUTHKEY));
    }

    if (!pnSecAuthAction(0, 'formicula::', '::', ACCESS_EDIT)) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_NOAUTH));
    }

    if(!empty($email) && !pnVarValidate($email, 'email')) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_ILLEGALEMAIL) . ': ' . pnVarPrepHTMLDisplay($email));
    }
    if(!empty($semail) && !pnVarValidate($semail, 'email')) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_ILLEGALEMAIL) . ': ' . pnVarPrepHTMLDisplay($semail));
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
        pnSessionSetVar('statusmsg', _FOR_CONTACTUPDATED);
    }
    pnRedirect(pnModURL('formicula', 'admin', 'view'));
    return true;
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
function formicula_admin_delete($args)
{

    list($cid,
         $confirmation) = pnVarCleanFromInput('cid',
                                              'confirmation');

    extract($args);

    $contact = pnModAPIFunc('formicula',
                            'admin',
                            'getContact',
                            array('cid' => $cid));

    if ($contact == false) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_NOSUCHCONTACT));
    }

    if (!pnSecAuthAction(0, 'formicula::', "::", ACCESS_DELETE)) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_NOAUTH));
    }

    // Check for confirmation.
    if (empty($confirmation)) {
        $pnr =& new pnRender('formicula');
        $pnr->caching = false;
        $contact['cid'] = $cid;
        $pnr->assign('contact', $contact);
        return $pnr->fetch('admindelete.html');
    }

    if (!pnSecConfirmAuthKey()) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_BADAUTHKEY));
    }

    if (pnModAPIFunc('formicula',
                     'admin',
                     'deleteContact',
                     array('cid' => $cid))) {
        // Success
        pnSessionSetVar('statusmsg', _FOR_CONTACTDELETED);
    }

    pnRedirect(pnModURL('formicula', 'admin', 'view'));

    return true;
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
    $pnr =& new pnRender('formicula');
    $pnr->caching = false;

    // read all items
    $allcontacts = pnModAPIFunc('formicula',
                                'admin',
                                'readContacts');
    // only use those where we have the necessary rights for
    $allowedcontacts = array();
    foreach ($allcontacts as $contact) {
        $cid = $contact['cid'];
        if (pnSecAuthAction(0, 'formicula::', "::$cid", ACCESS_EDIT)) {
            $allowedcontact = array('cid'        => $contact['cid'],
                                    'name'       => $contact['name'],
                                    'email'      => $contact['email'],
                                    'public'     => $contact['public'],
                                    'sname'      => $contact['sname'],
                                    'semail'     => $contact['semail'],
                                    'ssubject'   => $contact['ssubject'],
                                    'acc_edit'   => true,
                                    'acc_delete' => false);

            if (pnSecAuthAction(0, 'formicula::Contact', "$cid::", ACCESS_DELETE)) {
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
    if (!pnSecAuthAction(0, 'formicula::', '::', ACCESS_ADMIN)) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_NOAUTH));
    }

    $pnr =& new pnRender('formicula');
    $pnr->caching = false;
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
    if (!pnSecAuthAction(0, 'formicula::', '::', ACCESS_ADMIN)) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_NOAUTH));
    }

    $show_phone =        pnVarCleanFromInput('show_phone');
    $show_company =      pnVarCleanFromInput('show_company');
    $show_url =          pnVarCleanFromInput('show_url');
    $show_location =     pnVarCleanFromInput('show_location');
    $show_comment =      pnVarCleanFromInput('show_comment');
    $send_user =         pnVarCleanFromInput('send_user');
    $delete_file =       pnVarCleanFromInput('delete_file');
    $upload_dir =        pnVarCleanFromInput('upload_dir');
    $spamcheck =         pnVarCleanFromInput('spamcheck');
    $excludespamcheck =  pnVarCleanFromInput('excludespamcheck');

    if (!pnSecConfirmAuthKey()) {
        return showErrorMessage(pnVarPrepForDisplay(_FOR_BADAUTHKEY));
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

    pnRedirect(pnModURL('formicula', 'admin', 'modifyconfig'));
    return true;
}

?>