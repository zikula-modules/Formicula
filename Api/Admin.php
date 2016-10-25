<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Formicula_Api_Admin extends Zikula_AbstractApi
{
    /**
     * getContact
     * reads a single contact by id
     *
     * @param cid int contact id
     * @return array with contact information
     */
    public function getContact($args)
    {
        if (!isset($args['cid']) || empty($args['cid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Formicula::', ':'.(int)$args['cid'].':', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        return DBUtil::selectObjectByID('formcontacts', $args['cid'], 'cid');
    }

    /**
     * readContacts
     * reads the contact list and returns it as array
     *
     * @return array with contact information
     */
    public function readContacts()
    {
        // Security check
        if (!SecurityUtil::checkPermission('Formicula::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        $contacts = [];
        $dbtables = DBUtil::getTables();
        $contactscolumn = $dbtables['formcontacts_column'];
        $orderby = "ORDER BY $contactscolumn[cid]";

        $contacts = DBUtil::selectObjectArray('formcontacts', '', $orderby);

        // Return the contacts
        return $contacts;
    }

    /**
     * createContact
     * creates a new contact
     *
     * @param name  string name of the contact
     * @param email string email address
     * @param public int 0/1 to indicate if address is for public use
     * @param sname string use this as senders name in confirmation mails
     * @param semail string use this as senders email address in confirmation mails
     * @param ssubject string use this as subject in confirmation mails
     * @return boolean
     */
    public function createContact($args)
    {
        if (!System::isInstalling() && !SecurityUtil::checkPermission('Formicula::', "::", ACCESS_ADD)) {
            return LogUtil::registerPermissionError();
        }

        if ((!isset($args['name'])) || (!isset($args['email']))) {
            return LogUtil::registerArgsError();
        }
        if ((!isset($args['public'])) || empty($args['public'])) {
            $args['public'] = 0;
        }

        $obj = DBUtil::insertObject($args, 'formcontacts', 'cid');
        if (false === $obj) {
            return LogUtil::registerError(__('Error! Creation attempt failed.'));
        }
        $this->callHooks('item', 'create', $obj['cid']);

        return $obj['cid'];
    }

    /**
     * deleteContact
     * deletes a contact.
     *
     * @param cid int contact id
     * @return boolean
     */
    public function deleteContact($args)
    {
        if ((!isset($args['cid'])) || empty($args['cid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Formicula::', ':' . (int)$args['cid'] . ':', ACCESS_DELETE)) {
            return LogUtil::registerPermissionError();
        }

        $res = DBUtil::deleteObjectByID ('formcontacts', (int)$args['cid'], 'cid');
        if (false === $res) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }

        // Let any hooks know that we have deleted a contact
        $this->callHooks('item', 'delete', $args['cid']);

        // Let the calling process know that we have finished successfully
        return true;
    }


    /**
     * updateContact
     * updates a contact
     *
     * @param cid int contact id
     * @param name string name of the contact
     * @param email string email address
     * @return boolean
     */
    public function updateContact($args)
    {
        if (!isset($args['cid'])
            || !isset($args['name']) || empty($args['name'])
            || !isset($args['email']) || empty($args['email'])
        ) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Formicula::', ':' . (int)$args['cid'] . ':', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        if (!isset($args['public']) || empty($args['public'])) {
            $args['public'] = 0;
        }

        $res = DBUtil::updateObject($args, 'formcontacts', '', 'cid');
        if (false === $res) {
            return LogUtil::registerError($this->__('Error! Update attempt failed.'));
        }
        $this->callHooks('item', 'update', $args['cid']);

        return $args['cid'];
    }


    /**
     * getFormSubmits
     * reads the form submit list and returns it as array
     *
     * @return array with form submits information
     */
    public function getFormSubmits()
    {
        // Security check
        if (!SecurityUtil::checkPermission("Formicula::", "::", ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        $dbtables = DBUtil::getTables();
        $submitscolumn = $dbtables['formsubmits_column'];
        $orderby = "ORDER BY $submitscolumn[sid] DESC";

        return DBUtil::selectObjectArray('formsubmits', '', $orderby);
    }

    /**
     * getFormSubmits
     * reads the form submit list and returns it as array
     *
     * @return array with form submits information
     */
    public function getFormSubmit($args)
    {
        if (!isset($args['sid']) || empty($args['sid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (!SecurityUtil::checkPermission('Formicula::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        return DBUtil::selectObjectByID('formsubmits', $args['sid'], 'sid');
    }

    /**
     * deleteSubmit
     * deletes a form submit.
     *
     * @param sid int contact id
     * @return boolean
     */
    public function deleteSubmit($args)
    {
        if ((!isset($args['sid'])) || empty($args['sid'])) {
            return LogUtil::registerArgsError();
        }

        // Security check
        if (SecurityUtil::checkPermission('Formicula::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        $res = DBUtil::deleteObjectByID ('formsubmits', (int)$args['sid'], 'sid');
        if (false === $res) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }

        // Let the calling process know that we have finished successfully
        return true;
    }
}
