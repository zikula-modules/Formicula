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
// Original Author of file: Jim McDonald
// Purpose of file:  Template administration API
// ----------------------------------------------------------------------

include_once( "modules/Formicula/common.php" );

/**
 * getContact
 * reads a single contact by id
 *
 *@param cid int contact id
 *@returns array with contact information
 */
function Formicula_adminapi_getContact($args)
{
    extract($args);

    if (!isset($cid) || empty($cid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check - important to do this as early on as possible to 
    // avoid potential security holes or just too much wasted processing
    if (!pnSecAuthAction(0, "Formicula::", "::$cid", ACCESS_EDIT)) {
        return false;
    }

    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $contactstable  =  $pntable['formcontacts'];
    $contactscolumn = &$pntable['formcontacts_column'];

    $sql = "SELECT $contactscolumn[cid],
                   $contactscolumn[name],
                   $contactscolumn[email],
                   $contactscolumn[public]
            FROM $contactstable
            WHERE $contactscolumn[cid] = '" . (int)pnVarPrepForStore($cid) . "'";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _GETFAILED);
        return false;
    }

    if ($result->EOF) {
        return false;
    }

    list($cid, $name, $email, $public) = $result->fields;

    $result->Close();

    $contact = array('cid'    => $cid,
                     'name'   => $name,
                     'email'  => $email,
                     'public' => $public);
    return $contact;
}

/**
 * readContacts
 * reads the contact list and returns it as array
 *
 *@param none
 *@returns array with contact information
 */
function Formicula_adminapi_readContacts()
{
    $contacts = array();

    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $contactstable  =  $pntable['formcontacts'];
    $contactscolumn = &$pntable['formcontacts_column'];

    $sql = "SELECT $contactscolumn[cid],
                   $contactscolumn[name],
                   $contactscolumn[email],
                   $contactscolumn[public]
            FROM $contactstable
            ORDER BY $contactscolumn[cid]";
    $result = $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _GETFAILED);
        return false;
    }

    // Put items into result array.  Note that each item is checked
    // individually to ensure that the user is allowed access to it before it
    // is added to the results array
    for (; !$result->EOF; $result->MoveNext()) {
        list($cid, $name, $email, $public) = $result->fields;
        if (pnSecAuthAction(0, 'Formicula::', "::$cid", ACCESS_EDIT)) {
            $contacts[] = array('cid'    => $cid,
                                'name'   => $name,
                                'email'  => $email,
                                'public' => $public);
        }
    }

    $result->Close();

    // Return the contacts
    return $contacts;
}

/**
 * createContact
 * creates a new contact
 *
 *@param name string name of the contact
 *@param email strng email address
 *@returns boolean
 */
function Formicula_adminapi_createContact($args)
{
    extract($args);

    if ((!isset($name)) || (!isset($email))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
    if ((!isset($public)) || empty($public)) {
	$public = 0;
    }

    if (!pnSecAuthAction(0, 'Formicula::', "::", ACCESS_ADD)) {
        pnSessionSetVar('errormsg', _FOR_NOAUTH);
        return false;
    }

    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $contactstable  =  $pntable['formcontacts'];
    $contactscolumn = &$pntable['formcontacts_column'];

    // Get next ID in table - this is required prior to any insert that
    // uses a unique ID, and ensures that the ID generation is carried
    // out in a database-portable fashion
    $nextId = $dbconn->GenId($contactstable);

    $sql = "INSERT INTO $contactstable (
              $contactscolumn[cid],
              $contactscolumn[name],
              $contactscolumn[email],
              $contactscolumn[public])
            VALUES (
              $nextId,
              '" . pnVarPrepForStore($name) . "',
              '" . pnVarPrepForStore($email) . "',
              '" . (int)pnVarPrepForStore($public) . "')";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _CREATEFAILED);
        return false;
    }

    // Get the ID of the item that we inserted.  It is possible, although
    // very unlikely, that this is different from $nextId as obtained
    // above, but it is better to be safe than sorry in this situation
    $cid = $dbconn->PO_Insert_ID($contactstable, $contactscolumn['cid']);

    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'cid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
    pnModCallHooks('item', 'create', $cid, array('module' => 'Formicula'));

    return true;
}

/**
 * deleteContact
 * deletes a contact.
 *
 *@param cid int contact id
 *@returns boolean
 */
function Formicula_adminapi_deleteContact($args)
{
    extract($args);

    if ((!isset($cid)) || empty($cid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    // Security check 
    if (!pnSecAuthAction(0, 'Formicula::', "::$cid", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', _FOR_NOAUTH);
        return false;
    }

    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $contactstable  =  $pntable['formcontacts'];
    $contactscolumn = &$pntable['formcontacts_column'];

    $sql = "DELETE FROM $contactstable
            WHERE $contactscolumn[cid] = '" . (int)pnVarPrepForStore($cid) ."'";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _DELETEFAILED);
        return false;
    }

    // Let any hooks know that we have deleted an item.
    pnModCallHooks('item', 'delete', $cid, array('module' => 'Formicula'));

    return false;
}


/**
 * updateContact
 * updates a contact
 *
 *@param cid int contact id
 *@param name string name of the contact
 *@param email strng email address
 *@returns boolean
 */
function Formicula_adminapi_updateContact($args)
{
    extract($args);

    if ( (!isset($cid)) || (!isset($name)) || (!isset($email))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
    if ((!isset($public)) || empty($public)) {
	$public = 0;
    }

    // Security check 
    if (!pnSecAuthAction(0, 'Formicula::', "::$cid", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', _FOR_NOAUTH);
        return false;
    }

    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $contactstable  =  $pntable['formcontacts'];
    $contactscolumn = &$pntable['formcontacts_column'];

    $sql = "UPDATE $contactstable
            SET $contactscolumn[name]   = '".pnVarPrepForStore($name)."',
                $contactscolumn[email]  = '".pnVarPrepForStore($email)."',
		$contactscolumn[public] = '".(int)pnVarPrepForStore($public)."'
            WHERE $contactscolumn[cid]  = '".(int)$cid."'";
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _UPDATEFAILED);
        return false;
    }

    // Let any hooks know that we have updated an item.
    pnModCallHooks('item', 'update', $cid, array('module' => 'Formicula'));

    return false;
}

?>
