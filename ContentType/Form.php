<?php
/**
 * Formicula - the contact mailer for Zikula
 * -----------------------------------------
 *
 * @copyright  (c) Formicula Development Team
 * @link       https://github.com/zikula-ev/Formicula
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Frank Schummertz <frank@zikula.org>
 * @package    Third_Party_Components
 * @subpackage Formicula
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
            $form = ModUtil::func('Formicula', 'user', 'main', array('form' => (int)$this->form, 'cid' => $this->contact));

            return $form;
        }

        return DataUtil::formatForDisplay($this->__('No form selected'));
    }

    public function displayEditing()
    {
        if (isset($this->form)) {
            if ($this->contact > 0) {
                $output = '<p>' . $this->__f('The Formicula form #%1$s is shown here with only contact %2$s', array($this->form, $this->contact)) . '</p>';
            } else {
                $output = '<p>' . $this->__f('The Formicula form #%s is shown here with all contacts', $this->form) . '</p>';
            }

            return $output;
        }

        return DataUtil::formatForDisplay($this->__('No form selected'));
    }

    public function getDefaultData()
    {
        return array('form' => 0, 'contact' => -1);
    }

    public function startEditing()
    {
        $allContacts = ModUtil::apiFunc('Formicula', 'user', 'readValidContacts',
                                     array('form' => $this->form));
        $contacts = array();
        $contacts[] = array(
            'text' => $this->__('All public contacts or form default'),
            'value' => '-1'
        );

        foreach ($allContacts as $contact) {
            if ($contact['public']) {
                $contacts[] = array(
                    'text' => $contact['name'],
                    'value' => $contact['cid']
                );
            }
        }

        $this->view->assign('contacts', $contacts);
    }
}
