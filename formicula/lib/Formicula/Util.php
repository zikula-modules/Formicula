<?php

class Formicula_Util
{
    /**
     * envcheck
     * check some environment and set error messages
     */
    public static function envcheck()
    {
        $dom = ZLanguage::getModuleDomain('Formicula');

        if(!ModUtil::available('Mailer')) {
            LogUtil::registerError(__('Mailer module is not available - unable to send emails!', $dom));
        }

        if(ModUtil::getVar('formicula', 'spamcheck') <> 0) {
            $freetype = function_exists('imagettfbbox');
            if(!$freetype || ( !(imagetypes() && IMG_PNG)
                            && !(imagetypes() && IMG_JPG)
                            && !(imagetypes() && IMG_GIF)) ) {
                LogUtil::registerStatus(__('no image function available - captcha deactivated', $dom));
                ModUtil::setVar('Formicula', 'spamcheck', 0);
            }

            $cachedir = System::getVar('temp') . '/formicula_cache';
            if(!file_exists($cachedir) || !is_writable($cachedir)) {
                LogUtil::registerStatus(__('formicula_cache folder does not exist in Zikula\'s temporary folder or is not writable - captchas have been disabled', $dom));
                ModUtil::setVar('Formicula', 'spamcheck', 0);
            }
            if(!file_exists($cachedir.'/.htaccess')) {
                LogUtil::registerStatus(__('.htaccess file needed in formicula_cache folder not exist', $dom));
                ModUtil::setVar('Formicula', 'spamcheck', 0);
            }
        }
        return true;
    }
}