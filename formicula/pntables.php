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
// Localization and Adaption for PostNuke 0.750-Gold: Chris Hildebrandt
// Purpose of file:  Table information for Formicula module
// ----------------------------------------------------------------------


function formicula_pntables()
{
    // Initialise table array
    $pntable = array();

    // Get the name for the template item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $pntable['formcontacts'] = DBUtil::getLimitedTablename('formcontacts');

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $pntable['formcontacts_column'] = array('cid'      => 'pn_cid',
                                            'name'     => 'pn_name',
                                            'email'    => 'pn_email',
                                            'public'   => 'pn_public',
                                            'sname'    => 'pn_sname',
                                            'semail'   => 'pn_semail',
                                            'ssubject' => 'pn_ssubject');

    $pntable['formcontacts_column_def'] = array('cid'      => "I AUTO PRIMARY",
                                                'name'     => "C(40) NOTNULL DEFAULT ''",
                                                'email'    => "C(80) NOTNULL DEFAULT ''",
                                                'public'   => "I1 NOTNULL DEFAULT  0",
                                                'sname'    => "C(40) NOTNULL DEFAULT ''",
                                                'semail'   => "C(80) NOTNULL DEFAULT ''",
                                                'ssubject' => "C(80) NOTNULL DEFAULT ''");

    // Return the table information
    return $pntable;
}

?>