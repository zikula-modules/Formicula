<?php
/**
 * Formicula - the contact mailer for Zikula
 * -----------------------------------------
 *
 * @copyright  (c) Formicula Development Team
 * @link       http://code.zikula.org/formicula
 * @version    $Id: pnversion.php 131 2008-12-28 13:34:07Z Landseer $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Frank Schummertz <frank@zikula.org>
 * @package    Third_Party_Components
 * @subpackage formicula
 */

class Formicula_Form_Handler_Admin_Edit
{
    public $cid;

    function initialize(&$view)
    {
        $dom = ZLanguage::getModuleDomain('Formicula');
        $this->cid = (int)FormUtil::getPassedValue('cid', -1, 'GETPOST');

        $view->caching = false;
        $view->add_core_data();

        if(($this->cid==-1) ) {
            $mode = 'create';
            $contact = array('cid'   => -1);
        } else {
            $mode = 'edit';
            $contact = ModUtil::apiFunc('formicula',
                                    'admin',
                                    'getContact',
                                    array('cid' => $this->cid));
            if ($contact == false) {
                return LogUtil::registerError(__('Unknown Contact', $dom), null, ModUtil::url('formicula', 'admin', 'main'));
            }
        }

        $view->assign('mode', $mode);
        $view->assign('contact', $contact);

        return true;
    }


    function handleCommand(&$view, &$args)
    {
        $dom = ZLanguage::getModuleDomain('formicula');
        // Security check
        if (!SecurityUtil::checkPermission('formicula::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError(ModUtil::url('Formicula', 'admin', 'main'));
        }
        if ($args['commandName'] == 'submit') {
            $ok = $view->isValid();

            $data = $view->getValues();
            $data['cid'] = $this->cid;
            $data['public'] = (int)$data['public'];

            // copy cname to name for updating the db
            $data['name'] = $data['cname'];

            // no deletion, further checks needed
            if(empty($data['cname'])) {
                $ifield = & $view->getPluginById('cname');
                $ifield->setError(DataUtil::formatForDisplay(__('Error! No contact name', $dom)));
                $ok = false;
            }
            if(empty($data['email']) || !System::varValidate($data['email'], 'email')) {
                $ifield = & $view->getPluginById('email');
                $ifield->setError(DataUtil::formatForDisplay(__('Error! No or incorrect email address supplied', $dom)));
                $ok = false;
            }
            if(!empty($data['semail']) && !System::varValidate($data['semail'], 'email')) {
                $ifield = & $view->getPluginById('semail');
                $ifield->setError(DataUtil::formatForDisplay(__('Error! Incorrect email address supplied', $dom)));
                $ok = false;
            }

            if(!$ok) {
                return false;
            }

            // The API function is called
            if($data['cid'] == -1) {
                if(ModUtil::apiFunc('Formicula', 'admin', 'createContact', $data) <> false) {
                    // Success
                    LogUtil::registerStatus(__('Contact created', $dom));
                } else {
                    LogUtil::registerError(__('Error creating contact!', $dom));
                }
            } else {
                if(ModUtil::apiFunc('Formicula', 'admin', 'updateContact', $data) <> false) {
                    // Success
                    LogUtil::registerStatus(__('Contact info has been updated', $dom));
                } else {
                    LogUtil::registerError(__('Error updating contact!', $dom));
                }
            }

        }
        return System::redirect(ModUtil::url('Formicula', 'admin', 'main'));
    }

}
