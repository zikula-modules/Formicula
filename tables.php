<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function Formicula_tables()
{
    // Initialise table array
    $dbtable = [];

    // Get the name for the template item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $dbtable['formcontacts'] = 'formcontacts';

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $dbtable['formcontacts_column'] = [
        'cid'      => 'pn_cid',
        'name'     => 'pn_name',
        'email'    => 'pn_email',
        'public'   => 'pn_public',
        'sname'    => 'pn_sname',
        'semail'   => 'pn_semail',
        'ssubject' => 'pn_ssubject'
    ];

    $dbtable['formcontacts_column_def'] = [
        'cid'      => "I AUTO PRIMARY",
        'name'     => "C(40) NOTNULL DEFAULT ''",
        'email'    => "C(80) NOTNULL DEFAULT ''",
        'public'   => "I1 NOTNULL DEFAULT  0",
        'sname'    => "C(80) NOTNULL DEFAULT ''",
        'semail'   => "C(80) NOTNULL DEFAULT ''",
        'ssubject' => "C(80) NOTNULL DEFAULT ''"
    ];

    // Get the name for the template item table.
    $dbtable['formsubmits'] = 'formsubmits';

    // Set the column names.
    $dbtable['formsubmits_column'] = [
        'sid'     => 'pn_sid',
        'form'       => 'pn_form',
        'cid'        => 'pn_cid',
        'ip'         => 'pn_ip',
        'host'       => 'pn_host',
        'name'       => 'pn_name',
        'email'      => 'pn_email',
        'phone'      => 'pn_phone',
        'company'    => 'pn_company',
        'url'        => 'pn_url',
        'location'   => 'pn_location',
        'comment'    => 'pn_comment',
        'customdata' => 'pn_customdata'
    ];

    $dbtable['formsubmits_column_def'] = [
        'sid'     => "I AUTO PRIMARY",
        'form'       => "I NOTNULL",
        'cid'        => "I NOTNULL",
        'ip'         => "C(255) NOTNULL DEFAULT ''",
        'host'       => "C(255) NOTNULL DEFAULT ''",
        'name'       => "C(255) NOTNULL DEFAULT ''",
        'email'      => "C(255) NOTNULL DEFAULT ''",
        'phone'      => "C(255) NOTNULL DEFAULT ''",
        'company'    => "C(255) NOTNULL DEFAULT ''",
        'url'        => "C(255) NOTNULL DEFAULT ''",
        'location'   => "C(255) NOTNULL DEFAULT ''",
        'comment'    => "XL NOTNULL DEFAULT ''",
        'customdata' => "XL NOTNULL DEFAULT ''"
    ];

    ObjectUtil::addStandardFieldsToTableDefinition ($dbtable['formsubmits_column'], 'pn_');
    ObjectUtil::addStandardFieldsToTableDataDefinition($dbtable['formsubmits_column_def']);

    // Return the table information
    return $dbtable;
}
