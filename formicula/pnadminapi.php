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
// Purpose of file:  formicula administration API
// ----------------------------------------------------------------------

/**
 * getContact
 * reads a single contact by id
 *
 *@param cid int contact id
 *@returns array with contact information
 */
function formicula_adminapi_getContact($args)
{
    if (!isset($args['cid']) || empty($args['cid'])) {
        return LogUtil::registerError(_MODARGSERROR);
    }

    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!SecurityUtil::checkPermission('formicula::', ":$cid:", ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    $contact = DBUtil::selectObjectByID('formcontacts', $args['cid'], 'cid');
    return $contact;
}

/**
 * readContacts
 * reads the contact list and returns it as array
 *
 *@param none
 *@returns array with contact information
 */
function formicula_adminapi_readContacts()
{
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!SecurityUtil::checkPermission("formicula::", "::", ACCESS_READ)) {
        return LogUtil::registerPermissionError();
    }
    
    $contacts = array();
    $pntable =&pnDBGetTables();
    $contactscolumn = &$pntable['formcontacts_column'];
    $orderby = "ORDER BY $contactscolumn[cid]";
    
    $contacts = DBUtil::selectObjectArray('formcontacts', '', $orderby);

    // Return the contacts
    return $contacts;
}

/**
 * createContact
 * creates a new contact
 *
 *@param name  string name of the contact
 *@param email string email address
 *@param public int 0/1 to indicate if address is for public use
 *@param sname string use this as senders name in confirmation mails
 *@param semail string use this as senders email address in confirmation mails
 *@param ssubject string use this as subject in confirmation mails
 *@returns boolean
 */
function formicula_adminapi_createContact($args)
{
    if (!defined('_PNINSTALLVER') && !SecurityUtil::checkPermission('formicula::', "::", ACCESS_ADD)) {
        return LogUtil::registerPermissionError();
    }

    if ((!isset($args['name'])) || (!isset($args['email']))) {
        return LogUtil::registerError(_MODARGSERROR);
    }
    if ((!isset($args['public'])) || empty($args['public'])) {
	    $args['public'] = 0;
    }

    $obj = DBUtil::insertObject($args, 'formcontacts', 'cid');
    if($obj == false) {
        return LogUtil::registerError(_CREATEFAILED);
    }
    pnModCallHooks('item', 'create', $obj['cid']);
    return $obj['cid'];
}

/**
 * deleteContact
 * deletes a contact.
 *
 *@param cid int contact id
 *@returns boolean
 */
function formicula_adminapi_deleteContact($args)
{
    if ((!isset($args['cid'])) || empty($args['cid'])) {
        return LogUtil::registerError(_MODARGSERROR);
    }

    // Security check
    if (!SecurityUtil::checkPermission('formicula::', ':' . (int)$args['cid'] . ':', ACCESS_DELETE)) {
        return LogUtil::registerPermissionError();
    }

    $res = DBUtil::deleteObjectByID ('formcontacts', (int)$args['cid'], 'cid');
    if($res==false) {
        return LogUtil::registerError(_DELETEFAILED);
    }

    // Let any hooks know that we have deleted a contact
    pnModCallHooks('item', 'delete', $args['cid']);

    // Let the calling process know that we have finished successfully
    return true;
}


/**
 * updateContact
 * updates a contact
 *
 *@param cid int contact id
 *@param name string name of the contact
 *@param email string email address
 *@returns boolean
 */
function formicula_adminapi_updateContact($args)
{
    if ((!isset($args['cid'])) || 
        (!isset($args['name'])) || 
        (!isset($args['email']) ||
        (empty($args['name'])) ||
        (empty($args['email'])) )) {
        return LogUtil::registerError(_MODARGSERROR);
    }
    if ((!isset($args['public'])) || empty($args['public'])) {
	    $args['public'] = 0;
    }

    // Security check
    if (!SecurityUtil::checkPermission('formicula::', ':' . $args['cid'] . ':', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    $res = DBUtil::updateObject($args, 'formcontacts', '', 'cid');
    if($res == false) {
        return LogUtil::registerError(_MH_UPDATEFAILED);
    }
    pnModCallHooks('item', 'update', $args['cid']);
    return $args['cid'];
}

/**
 * get available admin panel links
 *
 * @author Mark West
 * @return array array of admin links
 */
function formicula_adminapi_getlinks()
{
    $links = array();
    if (SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADMIN)) {
        $links[] = array('url' => pnModURL('formicula', 'admin', 'view'), 'text' => _FOR_VIEWCONTACT);
        $links[] = array('url' => pnModURL('formicula', 'admin', 'edit', array('cid' => -1)), 'text' => _FOR_ADDCONTACT);
        $links[] = array('url' => pnModURL('formicula', 'admin', 'modifyconfig'), 'text' => _FOR_EDITCONFIG);
    }
    return $links;
}
