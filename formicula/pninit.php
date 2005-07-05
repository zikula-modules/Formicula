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
// Purpose of file:  Initialisation functions for template
// ----------------------------------------------------------------------


function formicula_init()
{
    pnModSetVar('formicula', 'show_phone', 1);
    pnModSetVar('formicula', 'show_company', 1);
    pnModSetVar('formicula', 'show_url', 1);
    pnModSetVar('formicula', 'show_location', 1);
    pnModSetVar('formicula', 'show_comment', 1);
    pnModSetVar('formicula', 'send_user', 1);
    $empty = array();
    pnModSetVar('formicula', 'Contacts', serialize( $empty ) );

    pnModSetVar('formicula', 'upload_dir', 'pnTemp');
    pnModSetVar('formicula', 'delete_file', 1);

    pnModSetVar('formicula', 'version', '0.4');
    // Initialisation successful
    return true;
}


function formicula_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1':
                pnModSetVar('formicula', 'upload_dir', 'pnTemp');
                pnModSetVar('formicula', 'delete_file', 1);
        case '0.2':
        case '0.3':
                // nothing to do

    }
    pnModSetVar('formicula', 'version', '0.4');

    // Update successful
    return true;
}


function formicula_delete()
{
    pnModDelVar('formicula', 'show_phone');
    pnModDelVar('formicula', 'show_company');
    pnModDelVar('formicula', 'show_url');
    pnModDelVar('formicula', 'show_location');
    pnModDelVar('formicula', 'show_comment');
    pnModDelVar('formicula', 'send_user');
    pnModDelVar('formicula', 'Contacts');
    pnModDelVar('formicula', 'upload_dir' );
    pnModDelVar('formicula', 'delete_file' );
    pnModDelVar('formicula', 'version' );
    return true;
}

?>