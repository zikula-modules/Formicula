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
// Purpose of file:  contact user display functions
// ----------------------------------------------------------------------

include_once( "modules/formicula/common.php" );

/**
 * main
 * main entry point for the user
 *
 *@param form int number of form to show
 *@returns pnRender output
 */
function formicula_user_main($args=array())
{
    if(count($args)>0) {
        extract($args);
    } else {
        $form = pnVarCleanFromInput( 'form' );
        $cid  = pnVarCleanFromInput( 'cid' );

        // get subitted information - will be passed to the template
        // addinfo is an array:
        // addinfo[name1] = value1
        // addinfo[name2] = value2
        $addinfo = pnVarCleanFromInput('addinfo');
    }
    $form = (!empty($form)) ? $form : 0;
    $cid  = (!empty($cid)) ? $cid : -1;

    if ($cid == -1) {
        $contacts = pnModAPIFunc('formicula',
                                 'user',
                                 'readValidContacts',
                                 array( 'form' => $form ) );
    } else {
        $contacts[] = pnModAPIFunc('formicula',
                                   'user',
                                   'getContact',
                                   array('cid'  => $cid,
				         'form' => $form ));
    }

    if ( count($contacts) == 0 ) {
        return showErrorMessage( pnVarPrepForDisplay(_FOR_NOAUTHFORFORM) );
    }

    if (pnUserLoggedIn()) {
        $uname = ( pnUserGetVar('name') == '' ) ? pnUserGetVar('uname') : pnUserGetVar('name');
        $uemail = pnUserGetVar('email');
    } else {
        $uname = '';
        $email = '';
    }

    $pnr =& new pnRender('formicula');
    $pnr->caching = false;
    $pnr->assign('uname', $uname);
    $pnr->assign('uemail', $uemail);
    $pnr->assign('contacts', $contacts);
    $pnr->assign('addinfo', $addinfo);
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

    if(count($args)>0) {
        extract($args);
    } else {
        list($cid,
           $form,
           $userformat,
           $adminformat,
           $numfields,
           $ud['uname'],
           $ud['uemail'],
           $ud['url'],
           $ud['phone'],
           $ud['company'],
           $ud['location'],
           $ud['comment']) = pnVarCleanFromInput('cid',
                                                 'form',
                                                 'userformat',
                                                 'adminformat',
                                                 'numFields',
                                                 'uname',
                                                 'uemail',
                                                 'url',
                                                 'phone',
                                                 'company',
                                                 'location',
                                                 'comment');
    }

    $form = (!empty($form)) ? $form : 0;
    if(empty($userformat) || ($userformat<>'plain' && $userformat<>'html' && $userformat<>'none')) {
        $userformat = 'plain';
    }
    if(empty($adminformat) || ($adminformat<>'plain' && $adminformat<>'html')) {
        $adminformat = 'plain';
    }

    if($userformat == 'none') {
        $ud['uemail'] = pnConfigGetVar('adminmail');
        $ud['uname'] =  pnConfigGetVar('adminmail');
    }

    if( !pnSecAuthAction(0, "formicula::", "$form::$cid", ACCESS_COMMENT) ) {
        return showErrorMessage( pnVarPrepForDisplay(_FOR_NOAUTHFORFORM) );
    }

    // addon: custom fields
    $uploaddir = pnModGetVar( 'formicula', 'upload_dir' );
    // check if it ends with / or we add one
    if( substr( $uploaddir, strlen($uploaddir)-1, 1 ) <> "/" ) {
        $uploaddir .= "/";
    }
    $custom = array();
    for( $i=0;$i<$numfields;$i++ ) {
        $custom[$i]['name'] = pnVarCleanFromInput( 'custom'.$i.'name' );
        $custom[$i]['mandatory'] = ( pnVarCleanFromInput( 'custom'.$i.'mandatory' ) == 1 ) ? true : false;

        if( isset( $_FILES['custom'.$i.'data']['tmp_name'] ) ) {
            $custom[$i]['data']['error'] = $_FILES['custom'.$i.'data']['error'];
            if( $custom[$i]['data']['error'] == 0 ) {
                $custom[$i]['data']['size']     = $_FILES['custom'.$i.'data']['size'];
                $custom[$i]['data']['type']     = $_FILES['custom'.$i.'data']['type'];
                $custom[$i]['data']['name']     = $_FILES['custom'.$i.'data']['name'];
                $custom[$i]['upload'] = true;
                move_uploaded_file( $_FILES['custom'.$i.'data']['tmp_name'], $uploaddir.$custom[$i]['data']['name'] );
            } else {
                // error - replace the 'data' with an errormessage
                $custom[$i]['data'] = constant("_FOR_UPLOADERROR".$custom[$i]['data']['error'] );
            }
        } else {
            $custom[$i]['data'] = pnVarCleanFromInput( 'custom'.$i.'data' );
            $custom[$i]['upload'] = false;
        }
    }

    $contact = pnModAPIFunc('formicula',
                            'user',
                            'getContact',
                            array('cid'  => $cid,
                                  'form' => $form ));

    $pnr =& new pnRender('formicula');
    $pnr->caching=false;
    $pnr->assign( 'contact', $contact );
    $pnr->assign( 'userdata', $ud );

    if( pnModAPIFunc('formicula',
                     'user',
                     'checkArguments',
                     array('userdata'   => $ud,
                           'custom'     => $custom ) ) == true ) {
        if( pnModAPIFunc('formicula',
                         'user',
                         'sendtoContact',
                         array('contact'  => $contact,
                               'userdata' => $ud,
                               'custom'   => $custom,
                               'form'     => $form,
                               'format'   => $adminformat ) ) == false ) {
            return showErrorMessage( pnVarPrepForDisplay(_FOR_ERRORSENDINGMAIL) );
        }

        if( (pnModGetVar('formicula', 'send_user') == 1) && ($userformat <> 'none') ) {
            // we replace the array of data of uploaded files with the filename
            $pnr->assign( 'sendtouser', pnModAPIFunc('formicula',
                                                     'user',
                                                     'sendtoUser',
                                                     array('contact'  => $contact,
                                                           'userdata' => $ud,
                                                           'custom'   => $custom,
                                                           'form'     => $form,
                                                           'format'   => $userformat  ) ) );
        }

        $pnr->assign( 'custom', removeUploadInformation( $custom ) );
        return $pnr->fetch( $form."_userconfirm.html" );
    } else {
        $pnr->assign( 'custom', removeUploadInformation( $custom ) );
        return $pnr->fetch( $form."_usererror.html" );
    }
}

?>
