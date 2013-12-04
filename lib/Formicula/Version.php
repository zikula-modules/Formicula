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

class Formicula_Version extends Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $meta = array();
        $meta['version'] = '3.1.0';
        $meta['core_min'] = '1.3.0'; // Fixed to 1.3.x range
        $meta['core_max'] = '1.3.99'; // Fixed to 1.3.x range
        $meta['oldnames']    = array('formicula');
        $meta['description'] = $this->__('Template-driven Form mailer');
        $meta['displayname'] = $this->__('Formicula');
        //! module url should be in lowercase without spaces different to displayname
        $meta['url'] = $this->__('formicula');
        $meta['capabilities'] = array(HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true));
        $meta['contact'] = 'Frank Schummertz <frank@zikula.org>';
        $meta['securityschema'] = array('Formicula::' => 'form_id:contact_id:' );
        return $meta;
    }

    protected function setupHookBundles()
    {
        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber.formicula.ui_hooks.forms', 'ui_hooks', $this->__('Formicula Form Hooks'));
        $bundle->addEvent('form_edit', 'formicula.ui_hooks.forms.form_edit');
        $bundle->addEvent('validate_edit', 'formicula.ui_hooks.forms.validate_edit');
        $this->registerHookSubscriberBundle($bundle);
    }

}