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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\FormiculaModule\Helper\EnvironmentHelper;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;

class CaptchaHelper
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var VariableApiInterface
     */
    private $variableApi;

    /**
     * @var PermissionApiInterface
     */
    private $permissionApi;

    /**
     * @var EnvironmentHelper
     */
    private $environmentHelper;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * TwigExtension constructor.
     *
     * @param TranslatorInterface    $translator        TranslatorInterface service instance
     * @param VariableApiInterface   $variableApi       VariableApi service instance
     * @param PermissionApiInterface $permissionApi     PermissionApi service instance
     * @param EnvironmentHelper      $environmentHelper EnvironmentHelper service instance
     * @param RouterInterface        $router            RouterInterface service instance
     * @param RequestStack           $requestStack      RequestStack service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        VariableApiInterface $variableApi,
        PermissionApiInterface $permissionApi,
        EnvironmentHelper $environmentHelper,
        RouterInterface $router,
        RequestStack $requestStack
    ) {
        $this->translator = $translator;
        $this->variableApi = $variableApi;
        $this->permissionApi = $permissionApi;
        $this->environmentHelper = $environmentHelper;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * Determines whether the spam check is active for a certain form.
     *
     * @param integer $form The form number
     *
     * @return boolean Whether the spam check is active or not
     */
    public function isSpamCheckEnabled($form = 0)
    {
        $enableSpamCheck = $this->variableApi->get('ZikulaFormiculaModule', 'enableSpamCheck', true);
        if ($enableSpamCheck) {
            $excludeSpamCheck = explode(',', $this->variableApi->get('ZikulaFormiculaModule', 'excludeSpamCheck', ''));
            if (is_array($excludeSpamCheck) && array_key_exists($form, array_flip($excludeSpamCheck))) {
                $enableSpamCheck = false;
            }
        }

        return $enableSpamCheck;
    }

    /**
     * Determines whether the given captcha code is correct or not.
     *
     * @param array   $operands The exercise operands
     * @param integer $captcha  The captcha code entered by the user
     *
     * @return boolean Whether the captcha is correct or not
     */
    public function isCaptchaValid(array $operands = [], $captcha = 0)
    {
        $captchaValid = false;
        if (!is_array($operands)) {
            return $captchaValid;
        }

        $op1 = (int)$operands['x'];
        $op2 = (int)$operands['y'];
        $op3 = (int)$operands['v'];

        switch ($operands['z'] . '-' . $operands['w']) {
            case '0-0':
                $captchaValid = ($op1 + $op2 + $op3 == $captcha);
                break;
            case '0-1':
                $captchaValid = ($op1 + $op2 - $op3 == $captcha);
                break;
            case '1-0':
                $captchaValid = ($op1 - $op2 + $op3 == $captcha);
                break;
            case '1-1':
                $captchaValid = ($op1 - $op2 - $op3 == $captcha);
                break;
            default:
                // $captchaValid is false
        }

        return $captchaValid;
    }

    /**
     * Creates a captcha image and returns it's markup code.
     *
     * based on imagetext (c) guite.de which is
     * based on imagetext (c) Christoph Erdmann <mail@cerdmann.com>
     *
     * @param string $font     Name of font to use (arial, freesans, quickhand, vera)
     * @param int    $size     Font size
     * @param string $bgColour Background colour (hex code without the # char)
     * @param string $fgColour Foreground colour (hex code without the # char)
     *
     * @return string The image markup
     */
    public function createCaptcha($font = 'quickhand', $size = 14, $bgColour = 'ffffff', $fgColour = '000000')
    {
        // check which image types are supported
        $freetype = function_exists('imagettfbbox');
        if ($freetype && (imagetypes() && IMG_PNG)) {
            $imageType = '.png';
            $createImageFunction = 'imagepng';
        } elseif ($freetype && (imagetypes() && IMG_JPG)) {
            $imageType = '.jpg';
            $createImageFunction = 'imagejpeg';
        } elseif ($freetype && (imagetypes() && IMG_GIF)) {
            $imageType = '.gif';
            $createImageFunction = 'imagegif';
        } else {
            // no image functions available
            $this->variableApi->set('ZikulaFormiculaModule', 'enableSpamCheck', false);
            if ($this->permissionApi->hasPermission('ZikulaFormiculaModule::', '.*', ACCESS_ADMIN)) {
                // admin permission, show error messages
                return '<p class="alert alert-danger">' . $this->translator->__('There are no image function available - Captchas have been disabled.') . '</p>';
            }

            // return silently
            return '';
        }

        // catch wrong input
        if (empty($font) || !is_numeric($size) || $size < 1 || empty($bgColour) || empty($fgColour)) {
            return '';
        }

        $fontPath = $this->getFontPath($font);
        if (!file_exists($fontPath) || !is_readable($fontPath)) {
            return '';
        }

        $operands = $this->determineOperands();

        $m = ['+', '-'];

        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            // store the numbers in a session var
            $request->getSession()->set('formiculaCaptcha', serialize($operands));
        }

        // create the text for the image
        $exerciseText = $operands['x'] . ' ' . $m[$operands['z']] . ' ' . $operands['y'] . ' ' . $m[$operands['w']] . ' ' . $operands['v'] . ' =';

        // hash the params for cache filename
        $hash = hash('sha256', $font . $size . $bgColour . $fgColour . $exerciseText);
        // create uri of image
        $cacheDirectory = $this->environmentHelper->getCacheDirectory();
        $imagePath = $cacheDirectory . '/' . $hash . $imageType;

        // create the image if it does not already exist
        if (!file_exists($imagePath)) {
            // we create a larger picture than needed, this makes it looking better at the end
            $multi = 4;
            // get the textsize in the image
            $bbox = imagettfbbox ($size * $multi, 0, $fontPath, $exerciseText);
            $xcorr = 0 - $bbox[6]; // northwest X
            $ycorr = 0 - $bbox[7]; // northwest Y
            $box['left'] = $bbox[6] + $xcorr;
            $box['height'] = abs($bbox[5]) + abs($bbox[1]);
            $box['width'] = abs($bbox[2]) + abs($bbox[0]);
            $box['top'] = abs($bbox[5]);

            // create the image
            $im = imagecreate ($box['width'], $box['height']);

            $bgcolor = $this->hexToRgb($im, $bgColour);
            $fgcolor = $this->hexToRgb($im, $fgColour);

            // add the text to the image
            imagettftext($im, $size * $multi, 0, $box['left'], $box['top'], $fgcolor, $fontPath, $exerciseText);

            // resize the image now
            $finalWidth  = round($box['width'] / $multi);
            $finalHeight = round($box['height'] / $multi);
            $ds = imagecreatetruecolor ($finalWidth, $finalHeight);

            $bgcolor2 = $this->hexToRgb($ds, $bgColour);
            imageFill($ds, 0, 0, $bgcolor2);
            imagecopyresampled($ds, $im, 0, $box['left'], 0, 0, $box['width'] / $multi, $box['height'] / $multi, $box['width'], $box['height']);
            imagetruecolortopalette($ds, 0, 256);
            imagepalettecopy($ds, $im);
            ImageColorTransparent($ds, $bgcolor);

            // write the file
            $createImageFunction($ds, $imagePath);
            ImageDestroy ($im);
            ImageDestroy ($ds);
        } else {
            // file already exists, calculate image size
            $imageData = getimagesize($imagePath);
            $finalWidth  = $imageData[0];
            $finalHeight = $imageData[1];
        }

        return '<img src="' . $this->router->generate('home') . $imagePath . '" alt="' . $this->translator->__('Math') . '" width="' . $finalWidth . '" height="' . $finalHeight . '" />';
    }

    /**
     * Returns the path to a given font.
     *
     * @param string $font Name of font to use
     *
     * @return string Path to the font file
     */
    private function getFontPath($font)
    {
        $absoluteModulePathParts = explode('/', str_replace('/Helper', '', __DIR__));
        $relativeModulePathParts = [];
        $moduleRootFound = false;
        foreach ($absoluteModulePathParts as $folder) {
            if ($folder == 'modules') {
                $moduleRootFound = true;
            }
            if ($moduleRootFound) {
                $relativeModulePathParts[] = $folder;
            }
        }
        $fontPath = implode('/', $relativeModulePathParts) . '/Resources/public/fonts/' . $font . '.ttf';

        return $fontPath;
    }

    /**
     * Determines random numbers for the calculation.
     *
     * @return array Random math exercise operands
     */
    private function determineOperands()
    {
        // x .z. y .w. v
        srand((double)microtime() * 1000000);
        $x = rand(1, 10);
        $y = rand(1, 10);
        $z = rand(0, 1);  /* 0=+, 1=- */
        $v = rand(1, 10);
        $w = rand(0, 1);  /* 0=+, 1=- */

        // turn minus into plus when x=y
        if ($z == 1 && $y == $x) {
            $z = 0;
        }

        // turn minus into plus when y=v
        if ($w == 1 && $v == $y) {
            $w = 0;
        }

        // make sure that x>y if z=1 (minus)
        if ($z == 1 && $y > $x) {
            $tmp = $x;
            $x = $y;
            $y = $tmp;
        }

        // turn minus into plus when v>x-y or v>x+y
        if ($w == 1 && ($z == 1 && $v > ($x - $y)) || ($z == 0 && $v > ($x + $y))) {
            $w = 0;
        }

        return [
            'x' => $x,
            'y' => $y,
            'z' => $z,
            'v' => $v,
            'w' => $w
        ];
    }

    /**
     * Converts a hex colour code into rgb values.
     *
     * @param resource $image   The image resource
     * @param string   $hexCode Colour hex code without the # char
     *
     * @return integer colour id determined by resulting rgb values
     */
    private function hexToRgb($image, $hexCode)
    {
        sscanf($hexCode, "%2x%2x%2x", $red, $green, $blue);

        return imagecolorallocate($image, $red, $green, $blue);
    }
}
