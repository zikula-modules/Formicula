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
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

/**
 * Content plugin class for displaying forms
 */
class FormType extends Content_AbstractContentType
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

            $fragmentHandler = new FragmentHandler(ServiceUtil::get('request_stack'));

            $ref = new ControllerReference('ZikulaFormiculaModule:User:index', [], [
                'form' => (int)$this->form,
                'cid' => $this->contact
            ]);

            return $fragmentHandler->render($ref, 'inline', []);
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
}
