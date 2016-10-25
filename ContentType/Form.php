<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Content plugin class for displaying forms
 */
class Formicula_ContentType_Form extends Content_AbstractContentType
{
    protected $form;

    protected $contact;

    public function getTitle()
    {
        return DataUtil::formatForDisplay($this->__('Formicula form'));
    }

    public function getDescription()
    {
        return DataUtil::formatForDisplay($this->__('Display a specific Formicula form'));
    }

    public function loadData(&$data)
    {
        $this->form = $data['form'];
        $this->contact = $data['contact'];
    }

    public function display()
    {
        if (isset($this->form)) {
            if (!isset($this->contact)) {
                $this->contact = -1;
            }
            PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('Formicula'));
            $form = ModUtil::func('Formicula', 'user', 'main', ['form' => (int)$this->form, 'cid' => $this->contact]);

            return $form;
        }

        return DataUtil::formatForDisplay($this->__('No form selected'));
    }

    public function displayEditing()
    {
        if (isset($this->form)) {
            if ($this->contact > 0) {
                $output = '<p>' . $this->__f('The Formicula form #%1$s is shown here with only contact %2$s', [$this->form, $this->contact)] . '</p>';
            } else {
                $output = '<p>' . $this->__f('The Formicula form #%s is shown here with all contacts', $this->form) . '</p>';
            }

            return $output;
        }

        return DataUtil::formatForDisplay($this->__('No form selected'));
    }

    public function getDefaultData()
    {
        return [
            'form' => 0,
            'contact' => -1
        ];
    }

    public function startEditing()
    {
        $allContacts = ModUtil::apiFunc('Formicula', 'user', 'readValidContacts', ['form' => $this->form]);
        $contacts = [];
        $contacts[] = [
            'text' => $this->__('All public contacts or form default'),
            'value' => '-1'
        ];

        foreach ($allContacts as $contact) {
            if ($contact['public']) {
                $contacts[] = [
                    'text' => $contact['name'],
                    'value' => $contact['cid']
                ];
            }
        }

        $this->view->assign('contacts', $contacts);
    }
}
