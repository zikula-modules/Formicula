<?php
// $Id: mh_admin_modifyconfighandler.class.php 166 2007-02-18 19:18:21Z landseer $
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
// Purpose of file:  MultiHook administration display functions
// ----------------------------------------------------------------------

class Formicula_admin_modifyconfighandler
{
    var $id;

    function initialize(&$pnRender)
    {
        $this->id = (int)FormUtil::getPassedValue('id', 0, 'GETPOST');
        $pnRender->caching = false;
        $pnRender->add_core_data();
        return true;
    }


    function handleCommand(&$pnRender, &$args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError('index.php');
        }  
        if ($args['commandName'] == 'submit') {
            if (!$pnRender->pnFormIsValid()) {
                return false;
            }
            $data = $pnRender->pnFormGetValues();
            if(!empty($data['upload_dir']) && !is_writable($data['upload_dir'])) {
                $ifield = & $pnRender->pnFormGetPluginById('upload_dir');
                $ifield->setError(DataUtil::formatForDisplay(_FOR_UPLOADDIRNOTWRITABLE));
                return false;
            }

            pnModSetVar('formicula', 'show_phone',       $data['show_phone']);
            pnModSetVar('formicula', 'show_company',     $data['show_company']);
            pnModSetVar('formicula', 'show_url',         $data['show_url']);
            pnModSetVar('formicula', 'show_location',    $data['show_location']);
            pnModSetVar('formicula', 'show_comment',     $data['show_comment']);
            pnModSetVar('formicula', 'send_user',        $data['send_user']);
            pnModSetVar('formicula', 'delete_file',      $data['delete_file']);
            pnModSetVar('formicula', 'upload_dir',       $data['upload_dir']);
            pnModSetVar('formicula', 'spamcheck',        $data['spamcheck']);
            pnModSetVar('formicula', 'excludespamcheck', $data['excludespamcheck']);
        }
        return true;
    }

}

?>