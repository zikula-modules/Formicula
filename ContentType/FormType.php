<?php

declare(strict_types=1);

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
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
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
    public function getIcon(): string
    {
        return 'list-ol';
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return $this->translator->trans('Formicula form');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->translator->trans('Display a specific Formicula form.');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'form' => 0,
            'contact' => -1
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function displayView(): string
    {
        $this->data = $this->getData();
        if (null === $this->data['form'] || (int) ($this->data['form']) < 0) {
            return '';
        }

        $attributes = [
            '_controller' => 'Zikula\FormiculaModule\Controller\UserController::indexAction',
            '_route' => 'zikulaformiculamodule_user_index'
        ];

        $subRequest = $this->requestStack->getCurrentRequest()->duplicate([
            'form' => (int)$this->data['form'],
            'cid' => $this->data['contact']
        ], null, $attributes);

        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

        return $response->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function displayEditing(): string
    {
        $this->data = $this->getData();
        if (null === $this->data['form'] || (int) ($this->data['form']) < 0) {
            return $this->translator->trans('No form selected.');
        }

        if ($this->data['contact'] > 0) {
            $output = '<p>' . $this->translator->trans('The Formicula form #%num% is shown here with only contact %con%', ['%num%' => $this->data['form'], '%con%' => $this->data['contact']]) . '</p>';
        } else {
            $output = '<p>' . $this->translator->trans('The Formicula form #%num% is shown here with all contacts', ['%num%' => $this->data['form']]) . '</p>';
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFormClass(): string
    {
        return EditFormType::class;
    }

    /**
     * @required
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @required
     */
    public function setHttpKernel(HttpKernelInterface $httpKernel)
    {
        $this->httpKernel = $httpKernel;
    }
}
