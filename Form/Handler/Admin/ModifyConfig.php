<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Formicula_Form_Handler_Admin_ModifyConfig extends Zikula_Form_AbstractHandler
{
    function initialize(Zikula_Form_View $view)
    {
        $view->caching = false;
        $view->add_core_data();

        // scan the tempaltes flder for installed forms
        $files = FileUtil::getFiles('modules/Formicula/templates/forms/', false, true, null, false);
        $sets_found = [];
        foreach ($files as $file) {
            $parts = explode('_', $file);
            if (is_array($parts) && count($parts) > 1) {
                if ($parts[0] == 'formicula') {
                    continue;
                }
                if ( !isset($sets_found[$parts[0]])) {
                    $sets_found[$parts[0]] = 0;
                }
                $sets_found[$parts[0]]++;
            }
        }

        $cachedir = System::getVar('temp') . '/formicula_cache';
        $view->assign('cachedir', $cachedir);

        $items = [];
        foreach ($sets_found as $formid => $files) {
            $items[] = [
                'text' => $this->__f('Form #%1$s that contains %2$s templates', [
                    'formid' => $formid,
                    'files' => $files
                ]),
                'value' => $formid
            ];
        }
        $view->assign('items', $items);

        $view->assign('default_userformatItems', [
            ['value' => 'html', 'text' => $this->__('HTML')],
            ['value' => 'plain', 'text' => $this->__('Text')]
        ]);

        $view->assign('default_adminformatItems', [
            ['value' => 'html', 'text' => $this->__('HTML')],
            ['value' => 'plain', 'text' => $this->__('Text')]
        ]);

        // assign all module vars
        $this->view->assign($this->getVars());

        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
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
            if (!empty($data['upload_dir']) && !is_writable($data['upload_dir'])) {
                $ifield = & $view->getPluginById('upload_dir');
                $ifield->setError(DataUtil::formatForDisplay($this->__('The webserver cannot write into this directory!')));
                return false;
            }

            // remove spaces in the comma seperated forms lists
            $data['excludespamcheck'] = preg_replace('/\s*/m', '', $data['excludespamcheck']);
            $data['store_data_forms'] = preg_replace('/\s*/m', '', $data['store_data_forms']);

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
            ModUtil::setVar('Formicula', 'store_data',       $data['store_data']);
            ModUtil::setVar('Formicula', 'store_data_forms', $data['store_data_forms']);
            ModUtil::setVar('Formicula', 'show_attachfile',  $data['show_attachfile']);
            ModUtil::setVar('Formicula', 'default_userformat', $data['default_userformat']);
            ModUtil::setVar('Formicula', 'default_adminformat', $data['default_adminformat']);
            ModUtil::setVar('Formicula', 'show_userformat',  $data['show_userformat']);
            ModUtil::setVar('Formicula', 'use_contacts_as_sender',  $data['show_userformat']);

            LogUtil::registerStatus($this->__('The configuration has been changed.'));
        }

        return true;
    }
}
