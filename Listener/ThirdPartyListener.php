<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Third party integration listener for the Formicula module
 */
class ThirdPartyListener implements EventSubscriberInterface
{
    /**
     * Listener for the `module.content.gettypes` event.
     *
     * This event occurs when the Content module is 'searching' for Content plugins.
     * The subject is an instance of Content_Types.
     * You can register custom content types as well as custom layout types.
     *
     * @param \Zikula_Event $event The event instance
     */
    public function contentGetTypes(\Zikula_Event $event)
    {
        // intended is using the add() method to add a plugin like below
        $types = $event->getSubject();
        $types->add('ZikulaFormiculaModule_ContentType_Form');
    }

    public static function getSubscribedEvents()
    {
        return [
            'module.content.gettypes' => 'contentGetTypes'
        ];
    }
}
