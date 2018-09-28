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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Zikula\Common\Content\AbstractContentType;
use Zikula\FormiculaModule\ContentType\Form\Type\FormType as EditFormType;

/**
 * Formicula form content type.
 */
class FormType extends AbstractContentType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'list-ol';
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->translator->__('Formicula form', 'zikulaformiculamodule');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->translator->__('Display a specific Formicula form.', 'zikulaformiculamodule');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'form' => 0,
            'contact' => -1
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function displayView()
    {
        $this->data = $this->getData();
        if (null === $this->data['form'] || empty($this->data['form'])) {
            return '';
        }

        $path = [
            '_controller' => 'ZikulaFormiculaModule:User:index'
        ];

        $subRequest = $this->requestStack->getCurrentRequest()->duplicate([
            'form' => (int)$this->form,
            'cid' => $this->contact
        ], null, $path);

        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

        return $response->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function displayEditing()
    {
        $this->data = $this->getData();
        if (null === $this->data['form'] || empty($this->data['form'])) {
            return $this->translator->__('No form selected.', 'zikulaformiculamodule');
        }

        if ($this->data['contact'] > 0) {
            $output = '<p>' . $this->translator->__f('The Formicula form #%1$s is shown here with only contact %2$s', ['%1$s' => $this->data['form'], '%2$s' => $this->data['contact']], 'zikulaformiculamodule') . '</p>';
        } else {
            $output = '<p>' . $this->translator->__f('The Formicula form #%s is shown here with all contacts', ['%s' => $this->data['form']], 'zikulaformiculamodule') . '</p>';
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFormClass()
    {
        return EditFormType::class;
    }

    /**
     * @param RequestStack $requestStack
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param HttpKernelInterface $httpKernel
     */
    public function setHttpKernel(HttpKernelInterface $httpKernel)
    {
        $this->httpKernel = $httpKernel;
    }
}
