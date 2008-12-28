<?php
// $Id: for_admin_modifyconfighandler.class.php 107 2008-05-23 09:22:59Z landseer $
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
// Purpose of file:  formicula configuration pnForm handler class
// ----------------------------------------------------------------------

class Formicula_admin_modifyconfighandler
{
    function initialize(&$pnRender)
    {
        $pnRender->caching = false;
        $pnRender->add_core_data();
        // scan the tempaltes flder for installed forms
        Loader::loadClass('FileUtil');
        $files = FileUtil::getFiles('modules/formicula/pntemplates/', false, true, null, false);
        $sets_found = array();
        foreach ($files as $file) {
            $parts = explode('_', $file);
            if (is_array($parts) && count($parts) > 1) {
                if ($parts[0] == 'formicula') {
                    continue;
                }
                if (!in_array($sets_found, $parts[0])) {
                    $sets_found[$parts[0]]++;
                }
            }
        }
        $items = array();
        foreach ($sets_found as $formid => $files) {
            $items[] = array('text' => pnML('_FOR_SETNUMBERXWITHYFILES', array('formid'=> $formid, 'files' => $files)), 'value' => $formid);
        }        
        $pnRender->assign('items', $items);       
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
            pnModSetVar('formicula', 'default_form',     $data['default_form']);
            
            LogUtil::registerStatus(_FOR_CONFIGURATIONCHANGED);
        }
        return true;
    }

}
