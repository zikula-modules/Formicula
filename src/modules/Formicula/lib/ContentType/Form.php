<?php
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

class Formicula_ContentType_Form extends Content_ContentType
{
    protected $form;

    public function getTitle()
    {
        return DataUtil::formatForDisplay($this->__('Formicula!'));
    }
    public function getDescription()
    {
        return DataUtil::formatForDisplay($this->__('Formicula!'));
    }
    public function loadData(&$data)
    {
        $this->form = $data['form'];
    }
    public function display()
    {
        if (isset($this->form)) {
            PageUtil::addVar('stylesheet', ThemeUtil::getModuleStylesheet('Formicula'));
            $form = ModUtil::func('Formicula', 'user', 'main', array('form' => (int)$this->form));
            return $form;
        }
        return DataUtil::formatForDisplay($this->__('No form selected'));
    }
    public function displayEditing()
    {
        $dom = ZLanguage::getModuleDomain('formicula');
        if (isset($this->form)) {
            $form = ModUtil::func('Formicula', 'user', 'main', array('form' => (int)$this->form));
            return $form;
        }
        return DataUtil::formatForDisplay($this->__('No form selected'));
    }
    public function getDefaultData()
    {
        return array('form' => 0);
    }
    public function startEditing(&$view)
    {
        return '';
    }
}