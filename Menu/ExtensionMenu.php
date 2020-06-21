<?php

declare(strict_types=1);

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\Menu;

use Knp\Menu\ItemInterface;
use Zikula\MenuModule\ExtensionMenu\AbstractExtensionMenu;

class ExtensionMenu extends AbstractExtensionMenu
{
    protected function getAdmin(): ?ItemInterface
    {
        $menu = $this->factory->createItem('formiculaAdminMenu');
        if ($this->permissionApi->hasPermission($this->getBundleName() . '::', '::', ACCESS_ADMIN)) {
            $menu->addChild('View contacts', [
                'route' => 'zikulaformiculamodule_contact_view',
            ])->setAttribute('icon', 'fas fa-users');
            $menu->addChild('Add contact', [
                'route' => 'zikulaformiculamodule_contact_edit',
            ])->setAttribute('icon', 'fas fa-user-plus');
            $menu->addChild('View submissions', [
                'route' => 'zikulaformiculamodule_submission_view',
            ])->setAttribute('icon', 'fas fa-envelope');
            $menu->addChild('Settings', [
                'route' => 'zikulaformiculamodule_config_config',
            ])->setAttribute('icon', 'fas fa-wrench');
            $menu->addChild('Clear captcha image cache', [
                'route' => 'zikulaformiculamodule_config_clearcache',
            ])->setAttribute('icon', 'fas fa-eraser');
        }

        return 0 === $menu->count() ? null : $menu;
    }

    public function getBundleName(): string
    {
        return 'ZikulaFormiculaModule';
    }
}
