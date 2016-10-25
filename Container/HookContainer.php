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

use Zikula\Bundle\HookBundle\AbstractHookContainer;
use Zikula\Bundle\HookBundle\Bundle\SubscriberBundle;

class HookContainer extends AbstractHookContainer
{
    protected function setupHookBundles()
    {
        $bundle = new SubscriberBundle('ZikulaFormiculaModule', 'subscriber.formicula.ui_hooks.forms', 'ui_hooks', $this->__('Formicula Form Hooks'));
        $bundle->addEvent('form_edit', 'formicula.ui_hooks.forms.form_edit');
        $bundle->addEvent('validate_edit', 'formicula.ui_hooks.forms.validate_edit');
        $this->registerHookSubscriberBundle($bundle);
    }
}
