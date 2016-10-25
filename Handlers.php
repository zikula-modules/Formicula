<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handlers for the Formicula module
 */
class Formicula_Handlers 
{
    // Content plugin for displaying forms
    public static function getTypes(Zikula_Event $event)
    {
        $types = $event->getSubject();
        $types->add('Formicula_ContentType_Form');
    }
}
