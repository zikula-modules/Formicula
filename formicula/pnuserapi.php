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
// Purpose of file:  Template user API
// ----------------------------------------------------------------------

include_once( "modules/formicula/common.php" );

/**
 * getContact
 * reads a single contact by id
 *
 *@param cid int contact id
 *@param form int form id
 *@returns array with contact information
 */
function formicula_userapi_getContact($args)
{
    extract($args);

    if (!isset($cid) || empty($cid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
    if (!isset($form) || empty($form)) {
        $form = 0;
    }

    if( !pnSecAuthAction(0, "formicula::", "$form::$cid", ACCESS_COMMENT) ) {
        return showErrorMessage( pnVarPrepForDisplay(_FOR_NOAUTHFORFORM) );
    }

    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $contactstable  =  $pntable['formcontacts'];
    $contactscolumn = &$pntable['formcontacts_column'];

    $sql = "SELECT $contactscolumn[cid],
                   $contactscolumn[name],
                   $contactscolumn[email]
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

    list($cid, $name, $email) = $result->fields;

    $result->Close();

    $contact = array('cid'    => $cid,
                     'name'   => $name,
                     'email'  => $email);
    return $contact;
}

/**
 * readValidContacts
 * reads the contact list and returns it as array.
 * This function filters out the entries the user is not allowed to see
 *
 *@param form int form id
 *@returns array with contact information
 */
function formicula_userapi_readValidContacts($args)
{
    extract($args);

    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $contactstable  =  $pntable['formcontacts'];
    $contactscolumn = &$pntable['formcontacts_column'];

    $sql = "SELECT $contactscolumn[cid],
                   $contactscolumn[name]
            FROM $contactstable
            WHERE $contactscolumn[public] = 1
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
    $contacts = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($cid, $name) = $result->fields;
        if (pnSecAuthAction(0, "formicula::", "$form::$cid", ACCESS_COMMENT)) {
            $contacts[] = array('cid'    => $cid,
                                'name'   => $name);
        }
    }

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the contacts
    return $contacts;
}

/**
 * sendtoContact
 * sends the mail to the contact
 *
 *@param userdata array with user submitted data
 *@param contact array of contact information (single contact)
 *@param custom array of custom fields information
 *@param form int form id
 *@param format   string email format, either 'plain' or 'html'
 *@returns boolean
 */
function formicula_userapi_sendtoContact($args)
{
    extract( $args );

    if(pnModAvailable('Mailer')) {
        $pnr =& new pnRender( 'formicula' );
        $pnr->caching = false;
        $ip = getenv('REMOTE_ADDR');
        $pnr->assign( 'host', gethostbyaddr($ip) );
        $pnr->assign( 'ip', $ip );
        $pnr->assign( 'form', $form );
        $pnr->assign( 'contact', $contact );
        $pnr->assign( 'userdata', $userdata );

        $adminmail = pnConfigGetVar('adminmail');
        $sitename = pnConfigGetVar('sitename');
        $pnr->assign( 'sitename', $sitename );

        // attach all files we have got
        $attachments = array();
        $uploaddir = pnModGetVar( 'formicula', 'upload_dir' );
        for( $i=0;$i<count($custom);$i++ ) {
            if( is_array( $custom[$i]['data'] ))  {
                $attachments[] = $uploaddir."/".$custom[$i]['data']['name'];
                $custom[$i]['data'] = $custom[$i]['data']['name'];
            }
        }
        $pnr->assign( 'custom', $custom );

        switch($format) {
            case 'html' :
                    $body = $pnr->fetch( $form."_adminmail.html" );
                    $html = true;
                    break;
            default:
                    $body = $pnr->fetch( $form."_adminmail.txt" );
                    $html = false;
        }

        $res = pnModAPIFunc( 'Mailer', 'user', 'sendmessage',
                             array( 'fromname'    => $userdata['uname'],
                                    'fromaddress' => $userdata['uemail'],
                                    'toname'      => $contact['mail'],
                                    'toaddress'   => $contact['email'],
                                    'subject'     => $sitename." - ".$contact['name'],
                                    'body'        => $body,
                                    'attachments' => $attachments,
                                    'html'        => $html ) );

        if( pnModGetVar( 'formicula', 'delete_file') == 1 ) {
            foreach( $attachments as $attachment ) {
                unlink( $attachment );
            }
        }

        if( $res == false ) {
            die("error!!");
        }
        return $res;

    }
    // no mailer module - error!
    return false;
}

/**
 * sendtoUser
 * sends the confirmation mail to the user
 *
 *@param userdata array with user submitted data
 *@param contact  array with contact data
 *@param custom   array of custom fields information
 *@param form     int form id
 *@param format   string email format, either 'plain' or 'html'
 *@returns boolean
 */
function formicula_userapi_sendtoUser($args)
{
    extract( $args );

    if(pnModAvailable('Mailer')) {
        $pnr =& new pnRender( 'formicula' );
        $pnr->caching = false;
        $ip = getenv('REMOTE_ADDR');
        $pnr->assign( 'host', gethostbyaddr($ip) );
        $pnr->assign( 'ip', $ip );
        $pnr->assign( 'form', $form );
        $pnr->assign( 'contact', $contact );
        $pnr->assign( 'userdata', $userdata );

        $adminmail = pnConfigGetVar('adminmail');
        $sitename = pnConfigGetVar('sitename');
        $pnr->assign( 'sitename', $sitename );

        $pnr->assign( 'custom', removeUploadInformation( $custom ) );

        switch($format) {
            case 'html' :
                    $body = $pnr->fetch( $form."_usermail.html" );
                    $html = true;
                    break;
            default:
                    $body = $pnr->fetch( $form."_usermail.txt" );
                    $html = false;
        }

        return pnModAPIFunc( 'Mailer', 'user', 'sendmessage',
                             array( 'fromname'    => sprintf( '%s - %s', $sitename, pnVarPrepForDisplay(_FOR_CONTACTFORM) ),
                                    'fromaddress' => $contact['email'],
                                    'toname'      => $userdata['uname'],
                                    'toaddress'   => $userdata['uemail'],
                                    'subject'     => $sitename." - ".$contact['name'],
                                    'body'        => $body,
                                    'html'        => $html ) );


    }
    // no mailer module - error!
    return false;
}

/**
 * checkArguments
 * checks if mandatory arguments are correct
 *@param userdata array with user submitted data, we are interested in uemail, uname and comment here
 *@param custom array with custom data
 *@returns boolean
 */
function formicula_userapi_checkArguments($args)
{
    extract($args);

    if (!isset($userdata['uemail']) || ( pnVarValidate( $userdata['uemail'], 'email' ) == false ) ) {
//die("no email");
        return false;
    }

    if (!isset($userdata['uname']) || ($userdata['uname'] == '')) {
//die("no name");
        return false;
    }

    foreach( $custom as $field ) {
        if( $field['mandatory'] == true ) {
            if( !is_array( $field['data'] ) && ( empty( $field['data'] ) ) ) {
//die("no " . $field['name']);
                return false;
            }
            if( ( $field['upload'] == true ) && ( $field['data']['size'] == 0 ) ) {
                return false;
            }
        }
    }
    return true;
}

?>
