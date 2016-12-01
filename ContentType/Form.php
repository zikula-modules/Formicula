<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\ContentType;

use DataUtil;
use ServiceUtil;
use Symfony\Component\HttpKernel\HttpKernelInterface;

if (!class_exists('Content_AbstractContentType')) {
    if (file_exists('modules/Content/lib/Content/AbstractContentType.php')) {
        require_once 'modules/Content/lib/Content/AbstractType.php';
        require_once 'modules/Content/lib/Content/AbstractContentType.php';
    } else {
        class Content_AbstractContentType {}
    }
}

/**
 * Content plugin class for displaying forms
 */
class Form extends \Content_AbstractContentType
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

            $path = [
                '_controller' => 'ZikulaFormiculaModule:User:index'
            ];

            $subRequest = ServiceUtil::get('request_stack')->getCurrentRequest()->duplicate([
                'form' => (int)$this->form,
                'cid' => $this->contact
            ], null, $path);

            return ServiceUtil::get('http_kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        return DataUtil::formatForDisplay($this->__('No form selected'));
    }

    public function displayEditing()
    {
        if (isset($this->form)) {
            if ($this->contact > 0) {
                $output = '<p>' . $this->__f('The Formicula form #%1$s is shown here with only contact %2$s', [$this->form, $this->contact]) . '</p>';
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
        $allContacts = ServiceUtil::get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\ContactEntity')->findBy([], ['name' => 'ASC']);
        $contacts = [];
        $contacts[] = [
            'text' => $this->__('All public contacts or form default'),
            'value' => '-1'
        ];

        // only use public contacts
        foreach ($allContacts as $contact) {
            if (!$contact->isPublic()) {
                continue;
            }

            $contacts[] = [
                'text' => $contact->getName(),
                'value' => $contact->getCid()
            ];
        }

        $this->view->assign('contacts', $contacts);
    }

    public function getEditTemplate()
    {
        $absoluteTemplatePath = str_replace('ContentType/Form.php', 'Resources/views/ContentType/form_edit.tpl', __FILE__);

        return 'file:' . $absoluteTemplatePath;
    }
}
