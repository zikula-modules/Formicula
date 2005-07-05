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

/**
 * showErrorMessage
 * shows an error message to the user or the admin
 *
 *@param text string the error message to show
 *@returns pnRender output
 */
function showErrorMessage($text)
{
    $pnr =& new pnRender( 'formicula' );
    $pnr->caching = false;
    $pnr->assign('errormsg', pnVarPrepForDisplay($text));
    return $pnr->fetch('errormessage.html');
}

/**
 * removeUploadInformation
 * replaces the information about uploaded files with the filename so that we can use it in the
 * templates
 *
 *@param custom array of custom fields
 *@return cleaned custom array
 */
function removeUploadInformation( $custom )
{
    for( $i=0;$i<count($custom);$i++ ) {
        if( $custom[$i]['upload'] == true ) {
            $custom[$i]['data'] = $custom[$i]['data']['name'];
        }
    }
    return $custom;
}

?>