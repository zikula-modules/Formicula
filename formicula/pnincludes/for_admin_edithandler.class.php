<?php
// $Id: mh_admin_edithandler.class.php 161 2007-01-28 17:00:20Z landseer $
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

class Formicula_admin_edithandler
{
    var $cid;

    function initialize(&$pnRender)
    {
        $this->cid = (int)FormUtil::getPassedValue('cid', -1, 'GETPOST');

        $pnRender->caching = false;
        $pnRender->add_core_data();
            
        if(($this->cid==-1) ) {
            $mode = 'create';
            $contact = array('cid'   => -1);
        } else {
            $mode = 'edit';
            $contact = pnModAPIFunc('formicula',
                                    'admin',
                                    'getContact',
                                    array('cid' => $this->cid));
            if ($contact == false) {
                return LogUtil::registerError(_FOR_NOSUCHCONTACT, null, pnModURL('formicula', 'admin', 'main'));
            }
        }

        $pnRender->assign('mode', $mode);
        $pnRender->assign('contact', $contact);

        return true;
    }


    function handleCommand(&$pnRender, &$args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError(pnModURL('formicula', 'admin', 'main'));
        }  
        if ($args['commandName'] == 'submit') {
            $ok = $pnRender->pnFormIsValid();

            $data = $pnRender->pnFormGetValues();
            $data['cid'] = $this->cid;
            $data['public'] = (int)$data['public'];
            
            // copy cname to name for updating the db
            $data['name'] = $data['cname'];

            // no deletion, further checks needed
            if(empty($data['cname'])) {
                $ifield = & $pnRender->pnFormGetPluginById('cname');
                $ifield->setError(DataUtil::formatForDisplay(_FOR_ERRORCONTACT));
                $ok = false;
            }
            if(empty($data['email']) || !pnVarValidate($data['email'], 'email')) {
                $ifield = & $pnRender->pnFormGetPluginById('email');
                $ifield->setError(DataUtil::formatForDisplay(_FOR_ERROREMAIL));
                $ok = false;
            }
            if(!empty($data['semail']) && !pnVarValidate($data['semail'], 'email')) {
                $ifield = & $pnRender->pnFormGetPluginById('semail');
                $ifield->setError(DataUtil::formatForDisplay(_FOR_ERRORINVALIDEMAIL));
                $ok = false;
            }
            
            if(!$ok) {
                return false;
            }

            // The API function is called
            if($data['cid'] == -1) {
                if(pnModAPIFunc('formicula', 'admin', 'createContact', $data) <> false) {
                    // Success
                    LogUtil::registerStatus(_FOR_CONTACTCREATED);
                } else {
                    LogUtil::registerError(_FOR_CREATECONTACTFAILED);
                }
            } else {
                if(pnModAPIFunc('formicula', 'admin', 'updateContact', $data) <> false) {
                    // Success
                    LogUtil::registerStatus(_FOR_CONTACTUPDATED);
                } else {
                    LogUtil::registerError(_FOR_UPDATECONTACTFAILED);
                }
            }

        }
        return pnRedirect(pnModURL('formicula', 'admin', 'main'));
    }

}
