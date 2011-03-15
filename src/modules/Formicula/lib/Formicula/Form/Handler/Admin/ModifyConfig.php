<?php
/**
 * Formicula - the contact mailer for Zikula
 * -----------------------------------------
 *
 * @copyright  (c) Formicula Development Team
 * @link       http://code.zikula.org/formicula
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Frank Schummertz <frank@zikula.org>
 * @package    Third_Party_Components
 * @subpackage formicula
 */

class Formicula_Form_Handler_Admin_ModifyConfig extends Zikula_Form_Handler
{
    function initialize($view)
    {
        $view->caching = false;
        $view->add_core_data();
        // scan the tempaltes flder for installed forms
        $files = FileUtil::getFiles('modules/Formicula/templates/', false, true, null, false);
        $sets_found = array();
        foreach ($files as $file) {
            $parts = explode('_', $file);
            if (is_array($parts) && count($parts) > 1) {
                if ($parts[0] == 'formicula') {
                    continue;
                }
                if( !isset($sets_found[$parts[0]])) {
                    $sets_found[$parts[0]] = 0;
                }
                $sets_found[$parts[0]]++;
            }
        }
        $items = array();
        foreach ($sets_found as $formid => $files) {
            $items[] = array('text' => $this->__f('Set form #%1$s with %2$s templates', array('formid'=> $formid, 'files' => $files)), 'value' => $formid);
        }
        $view->assign('items', $items);
        return true;
    }


    function handleCommand($view, &$args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('Formicula::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError(System::getHomepageUrl());
        }
        if ($args['commandName'] == 'submit') {
            if (!$view->isValid()) {
                return false;
            }
            $data = $view->getValues();
            if(!empty($data['upload_dir']) && !is_writable($data['upload_dir'])) {
                $ifield = & $view->pnFormGetPluginById('upload_dir');
                $ifield->setError(DataUtil::formatForDisplay($this->__('The webserver cannot write into this folder!')));
                return false;
            }

            ModUtil::setVar('Formicula', 'show_phone',       $data['show_phone']);
            ModUtil::setVar('Formicula', 'show_company',     $data['show_company']);
            ModUtil::setVar('Formicula', 'show_url',         $data['show_url']);
            ModUtil::setVar('Formicula', 'show_location',    $data['show_location']);
            ModUtil::setVar('Formicula', 'show_comment',     $data['show_comment']);
            ModUtil::setVar('Formicula', 'send_user',        $data['send_user']);
            ModUtil::setVar('Formicula', 'delete_file',      $data['delete_file']);
            ModUtil::setVar('Formicula', 'upload_dir',       $data['upload_dir']);
            ModUtil::setVar('Formicula', 'spamcheck',        $data['spamcheck']);
            ModUtil::setVar('Formicula', 'excludespamcheck', $data['excludespamcheck']);
            ModUtil::setVar('Formicula', 'default_form',     $data['default_form']);

            LogUtil::registerStatus($this->__('The configuration has been changed.'));
        }
        return true;
    }

}
