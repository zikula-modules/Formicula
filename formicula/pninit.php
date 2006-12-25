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
// Purpose of file:  Initialisation functions for formicula
// ----------------------------------------------------------------------


function formicula_init()
{
    // create the formicula table
    if (!DBUtil::createTable('formcontacts')) {
        return false;
    }

    pnModAPILoad('formicula', 'admin', true);
    pnModAPIFunc('formicula',
                 'admin',
                 'createContact',
                 array('name'     => 'Webmaster',
                       'email'    => pnConfigGetVar('adminmail'),
                       'public'   => 1,
                       'sname'    => 'Webmaster',
                       'semail'   => pnConfigGetVar('adminmail'),
                       'ssubject' => _FOR_EMAILFROM . ' %s'));

    pnModSetVar('formicula', 'show_phone', 1);
    pnModSetVar('formicula', 'show_company', 1);
    pnModSetVar('formicula', 'show_url', 1);
    pnModSetVar('formicula', 'show_location', 1);
    pnModSetVar('formicula', 'show_comment', 1);
    pnModSetVar('formicula', 'send_user', 1);
    pnModSetVar('formicula', 'spamcheck', 1);

    pnModSetVar('formicula', 'upload_dir', 'pnTemp');
    pnModSetVar('formicula', 'delete_file', 1);

    // Initialisation successful
    return true;
}


function formicula_upgrade($oldversion)
{
    // Get database information
    pnModDBInfoLoad('formicula');

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.1':
                pnModSetVar('formicula', 'upload_dir', 'pnTemp');
                pnModSetVar('formicula', 'delete_file', 1);
        case '0.2':
                // nothing to do
        case '0.3':
                // nothing to do
        case '0.4':
                // create the formicula table
                if (!DBUtil::createTable('formcontacts')) {
                    return false;
                }

                // migrate contacts from config var to table
                $contacts = pnModGetVar('formicula', 'contacts');
                if( @unserialize( $contacts ) != "" ) {
                    $contacts_array = unserialize( $contacts );
                } else {
                    $contacts_array = array();
                }
                foreach ($contacts_array as $contact) {
                    $name  = pnVarPrepForStore($contact['name']);
                    $email = pnVarPrepForStore($contact['email']);
                    pnModAPIFunc('formicula',
                                 'admin',
                                 'createContact',
                                 array('name'     => $name,
                                       'email'    => $email,
                                       'public'   => 1,
                                       'sname'    => '',   
                                       'semail'   => '',
                                       'ssubject' => ''));
                }
                pnModDelVar('formicula', 'contacts'); 
                pnModDelVar('formicula', 'version' );
        case '0.5':
                // nothing to do
        case '0.6':
                $dbconn  =& pnDBGetConn(true);
                $pntable =& pnDBGetTables();
            
                $contactstable  =  $pntable['formcontacts'];
                $contactscolumn = &$pntable['formcontacts_column'];
                
                $sql = "ALTER TABLE $contactstable
                        ADD $contactscolumn[sname]     varchar(40) NOT NULL default '',
                        ADD $contactscolumn[semail]    varchar(80) NOT NULL default '',
                        ADD $contactscolumn[ssubject]  varchar(80) NOT NULL default ''";
                $dbconn->Execute($sql);
                if ($dbconn->ErrorNo() != 0) {
                    pnSessionSetVar('errormsg', _FOR_ALTERTABLEFAILED);
                    return false;
                }
                pnModSetVar('formicula', 'spamcheck', 1);
                pnModSetVar('formicula', 'excludespamcheck', '');
        case '1.0':
            // nothing to do
        case '1.1':
            if (!DBUtil::changeTable('groups')) {
                return LogUtil::registerError(_MH_UPGRADETO50FAILED);
            }
    }

    // clear compiled templates
    pnModAPIFunc('pnRender', 'user', 'clear_compiled');

    // Update successful
    return true;
}


function formicula_delete()
{
    // drop the table
    if (!DBUtil::dropTable('formcontacts')) {
        return false;
    }

    // Remove module variables
    pnModDelVar('formicula');

    return true;
}

?>