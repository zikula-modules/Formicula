<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Formicula_Util
{
    /**
     * envcheck
     * check some environment and set error messages
     */
    public static function envcheck()
    {
        if (!ModUtil::available('ZikulaMailerModule')) {
            LogUtil::registerError(__('Mailer module is not available - unable to send emails!'));
        }

        if (ModUtil::getVar('Formicula', 'spamcheck', 1) == 0) {
            return true;
        }

        if (!function_exists('imagettfbbox') || ( !(imagetypes() && IMG_PNG)
                && !(imagetypes() && IMG_JPG)
                && !(imagetypes() && IMG_GIF)) ) {
            LogUtil::registerStatus(__('no image function available - captcha deactivated'));
            ModUtil::setVar('Formicula', 'spamcheck', 0);
        }

        $cachedir = System::getVar('temp') . '/formicula_cache';
        if (!file_exists($cachedir) || !is_writable($cachedir)) {
            LogUtil::registerStatus(__('Formicula_cache directory does not exist in Zikula\'s temporary directory or is not writable - captchas have been disabled'));
            ModUtil::setVar('Formicula', 'spamcheck', 0);
        }
        if (!file_exists($cachedir . '/.htaccess')) {
            LogUtil::registerStatus(__('.htaccess file needed in formicula_cache directory not exist'));
            ModUtil::setVar('Formicula', 'spamcheck', 0);
        }

        return true;
    }
}
