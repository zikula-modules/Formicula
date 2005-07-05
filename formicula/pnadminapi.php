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

include_once( "modules/formicula/common.php" );

/**
 * readContacts
 * reads the contact list and returns it as array
 *
 *@param none
 *@returns array with contact information
 */
function formicula_adminapi_readContacts()
{
    $contacts = pnModGetVar( 'formicula', 'contacts' );
    if( @unserialize( $contacts ) != "" ) {
        return unserialize( $contacts );
    }
    return array();
}

/**
 * storeContacts
 * store the array of contacts
 *
 *@param contacts array of contacts
 *@returns true
 */
function formicula_adminapi_storeContacts($args)
{
    pnModSetVar( 'formicula', 'contacts', serialize( $args['contacts'] ) );
    return true;
}

/**
 * createContact
 * creates a new contact
 *
 *@param name string name of the contact
 *@param email strng email address
 *@returns boolean
 */
function formicula_adminapi_createContact($args)
{
    extract($args);

    if ((!isset($name)) || (!isset($email))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    if (!pnSecAuthAction(0, 'formicula::', "::", ACCESS_ADD)) {
        pnSessionSetVar('errormsg', _FOR_NOAUTH);
        return false;
    }

    $contacts = formicula_adminapi_readContacts();
    $newcontact = array( 'name'  => $name,
                         'email' => $email );
    array_push( $contacts, $newcontact );
    formicula_adminapi_storeContacts( array( 'contacts' => $contacts ) );
    return true;
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
    extract($args);

    if (!isset($cid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $contacts = formicula_adminapi_readContacts();
    if( count( $contacts ) > 0 ) {
        if (pnSecAuthAction(0, 'formicula::', "::", ACCESS_DELETE)) {
            $contacts[$cid] = false;
            formicula_adminapi_storeContacts( array( 'contacts' => $contacts ) );
            return true;
        } else {
            pnSessionSetVar('errormsg', _FOR_NOAUTH);
            return false;
        }
    }
    pnSessionSetVar('errormsg', _FOR_NOCONTACTS);
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
function formicula_adminapi_updateContact($args)
{
    extract($args);

    if ( (!isset($cid)) || (!isset($name)) || (!isset($email))) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $contacts = formicula_adminapi_readContacts();
    if( count( $contacts ) > 0 ) {
        if( $contacts[$cid] <> false ) {
            if (pnSecAuthAction(0, 'formicula::', "::", ACCESS_EDIT)) {
                $contacts[$cid]['name']  = $name;
                $contacts[$cid]['email'] = $email;
                formicula_adminapi_storeContacts( array( 'contacts' => $contacts ) );
                return true;
            } else {
                pnSessionSetVar('errormsg', _FOR_NOAUTH);
                return false;
            }
        } else {
            pnSessionSetVar('errormsg', _FOR_NOSUCHCONTACT);
            return false;
        }

    }
    pnSessionSetVar('errormsg', _FOR_NOCONTACTS);
    return false;

}

?>