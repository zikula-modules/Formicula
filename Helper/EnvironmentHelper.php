<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\Helper;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ExtensionsModule\Api\VariableApi;

class EnvironmentHelper
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var VariableApi
     */
    private $variableApi;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * Constructor.
     *
     * @param KernelInterface     $kernel      KernelInterface service instance
     * @param TranslatorInterface $translator  TranslatorInterface service instance
     * @param VariableApi         $variableApi VariableApi service instance
     * @param SessionInterface    $session     SessionInterface service instance
     */
    public function __construct(KernelInterface $kernel, TranslatorInterface $translator, VariableApi $variableApi, SessionInterface $session) {
        $this->kernel = $kernel;
        $this->translator = $translator;
        $this->variableApi = $variableApi;
        $this->session = $session;
    }

    /**
     * Checks some environment aspects and sets error messages.
     */
    public function check()
    {
        $flashBag = $this->session->getFlashBag();
        if (null === $this->kernel->getModule('ZikulaMailerModule')) {
            $flashBag->add('error', $this->translator->__('Mailer module is not available - unable to send emails!'));
        }

        if (false === $this->variableApi->get('ZikulaFormiculaModule', 'enableSpamCheck', true)) {
            return;
        }

        if (!function_exists('imagettfbbox')
            || (!(imagetypes() && IMG_PNG) && !(imagetypes() && IMG_JPG) && !(imagetypes() && IMG_GIF))
        ) {
            $flashBag->add('status', $this->translator->__('There are no image function available - Captchas have been disabled.'));
            $this->variableApi->set('ZikulaFormiculaModule', 'enableSpamCheck', false);
        }

        $cacheDirectory = $this->getCacheDirectory();
        if (!file_exists($cacheDirectory) || !is_writable($cacheDirectory)) {
            $flashBag->add('status', $this->translator->__('Formicula cache directory does not exist or is not writable - Captchas have been disabled.'));
            $this->variableApi->set('ZikulaFormiculaModule', 'enableSpamCheck', false);
        } elseif (!file_exists($cacheDirectory . '/.htaccess')) {
            $flashBag->add('status', $this->translator->__('Formicula cache directory does not contain the required .htaccess file - Captchas have been disabled.'));
            $this->variableApi->set('ZikulaFormiculaModule', 'enableSpamCheck', false);
        }
    }

    /**
     * Returns path to cache directory.
     *
     * @return string Path to temporary cache directory
     */
    public function getCacheDirectory()
    {
        return 'app/cache/formicula';
    }
}
