<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\Container;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\Translator;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\PermissionsModule\Api\PermissionApi;

class LinkContainer implements LinkContainerInterface
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PermissionApi
     */
    private $permissionApi;

    /**
     * LinkContainer constructor.
     *
     * @param Translator      $translator    Translator service instance
     * @param RouterInterface $router        RouterInterface service instance
     * @param PermissionApi   $permissionApi PermissionApi service instance
     */
    public function __construct($translator, RouterInterface $router, PermissionApi $permissionApi)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->permissionApi = $permissionApi;
    }

    /**
     * get Links of any type for this extension
     * required by the interface
     *
     * @param string $type
     * @return array
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $method = 'get' . ucfirst(strtolower($type));
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return [];
    }

    /**
     * get the Admin links for this extension
     *
     * @return array
     */
    private function getAdmin()
    {
        $links = [];

        if (!$this->permissionApi->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            return $links;
        }

        $links[] = [
            'url' => $this->router->generate('zikulaformiculamodule_contact_view'),
            'text' => $this->translator->__('View contacts'),
            'class' => 'group'
        ];
        $links[] = [
            'url' => $this->router->generate('zikulaformiculamodule_contact_edit', ['cid' => -1]),
            'text' => $this->translator->__('Add contact'),
            'class' => 'user-plus'
        ];
        $links[] = [
            'url' => $this->router->generate('zikulaformiculamodule_submission_view'),
            'text' => $this->translator->__('View form submits'),
            'class' => 'envelope-open'
        ];
        $links[] = [
            'url' => $this->router->generate('zikulaformiculamodule_config_config'),
            'text' => $this->translator->__('Settings'), 
            'class' => 'wrench',
            'links' => [
                [
                    'url' => $this->router->generate('zikulaformiculamodule_config_config'),
                    'text' => $this->translator->__('Settings'), 
                    'class' => 'wrench'
                ],
                [
                    'url' => $this->router->generate('zikulaformiculamodule_config_clearcache'),
                    'text' => $this->translator->__('Clear captcha image cache'), 
                    'class' => 'eraser'
                ]
            ]
        ];

        return $links;
    }

    /**
     * set the BundleName as required by the interface
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'ZikulaFormiculaModule';
    }
}
