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

namespace Zikula\FormiculaModule\Helper;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
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
     * @var string
     */
    private $projectDirectory;

    /**
     * @var string
     */
    private $cacheDirectory;

    public function __construct(
        TranslatorInterface $translator,
        VariableApiInterface $variableApi,
        PermissionApiInterface $permissionApi,
        EnvironmentHelper $environmentHelper,
        RouterInterface $router,
        RequestStack $requestStack,
        string $projectDir,
        string $cacheDir
    ) {
        $this->translator = $translator;
        $this->variableApi = $variableApi;
        $this->permissionApi = $permissionApi;
        $this->environmentHelper = $environmentHelper;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->projectDirectory = $projectDir;
        $this->cacheDirectory = $cacheDir;
    }

    /**
     * Determines whether the spam check is active for a certain form.
     */
    public function isSpamCheckEnabled(int $form = 0): bool
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
     * Determines whether a given captcha code is correct or not.
     */
    public function isCaptchaValid(array $operands = [], int $captcha = 0): bool
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
                $captchaValid = ($op1 + $op2 + $op3 === $captcha);
                break;
            case '0-1':
                $captchaValid = ($op1 + $op2 - $op3 === $captcha);
                break;
            case '1-0':
                $captchaValid = ($op1 - $op2 + $op3 === $captcha);
                break;
            case '1-1':
                $captchaValid = ($op1 - $op2 - $op3 === $captcha);
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
    public function createCaptcha(
        string $font = 'quickhand',
        int $size = 14,
        string $bgColour = 'ffffff',
        string $fgColour = '000000'
    ): string {
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
                return '<p class="alert alert-danger">' . $this->translator->trans('There are no image function available - Captchas have been disabled.') . '</p>';
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
        $imagePath = $this->cacheDirectory . '/' . $hash . $imageType;

        // create the image if it does not already exist
        if (!file_exists($imagePath)) {
            // we create a larger picture than needed, this makes it looking better at the end
            $multi = 4;
            // get the textsize in the image
            $bbox = imagettfbbox($size * $multi, 0, $fontPath, $exerciseText);
            $xcorr = 0 - $bbox[6]; // northwest X
            $ycorr = 0 - $bbox[7]; // northwest Y
            $box['left'] = $bbox[6] + $xcorr;
            $box['height'] = abs($bbox[5]) + abs($bbox[1]);
            $box['width'] = abs($bbox[2]) + abs($bbox[0]);
            $box['top'] = abs($bbox[5]);

            // create the image
            $im = imagecreate($box['width'], $box['height']);

            $bgcolor = $this->hexToRgb($im, $bgColour);
            $fgcolor = $this->hexToRgb($im, $fgColour);

            // add the text to the image
            imagettftext($im, $size * $multi, 0, $box['left'], $box['top'], $fgcolor, $fontPath, $exerciseText);

            // resize the image now
            $finalWidth = (int)round($box['width'] / $multi);
            $finalHeight = (int)round($box['height'] / $multi);
            $ds = imagecreatetruecolor($finalWidth, $finalHeight);

            $bgcolor2 = $this->hexToRgb($ds, $bgColour);
            imagefill($ds, 0, 0, $bgcolor2);
            imagecopyresampled($ds, $im, 0, $box['left'], 0, 0, (int)($box['width'] / $multi), (int)($box['height'] / $multi), $box['width'], $box['height']);
            imagetruecolortopalette($ds, false, 256);
            imagepalettecopy($ds, $im);
            imagecolortransparent($ds, $bgcolor);

            // write the file
            $createImageFunction($ds, $imagePath);
            imagedestroy($im);
            imagedestroy($ds);
        } else {
            // file already exists, calculate image size
            $imageData = getimagesize($imagePath);
            $finalWidth  = $imageData[0];
            $finalHeight = $imageData[1];
        }

        $relativeImagePath = str_replace($this->projectDirectory, '', $imagePath);

        return '<img src="' . $relativeImagePath . '" alt="' . $this->translator->trans('Math') . '" width="' . $finalWidth . '" height="' . $finalHeight . '" />';
    }

    /**
     * Returns the path to a given font.
     *
     * @param string $font Name of font to use
     *
     * @return string Path to the font file
     */
    private function getFontPath(string $font): string
    {
        $absolutePath = dirname(__DIR__);
        $fontPath = $absolutePath . '/Resources/public/fonts/' . $font . '.ttf';

        return $fontPath;
    }

    /**
     * Determines random numbers for the calculation.
     *
     * @return array Random math exercise operands
     */
    private function determineOperands(): array
    {
        // x .z. y .w. v
        mt_srand((int)microtime() * 1000000);
        $x = mt_rand(1, 10);
        $y = mt_rand(1, 10);
        $z = mt_rand(0, 1);  /* 0=+, 1=- */
        $v = mt_rand(1, 10);
        $w = mt_rand(0, 1);  /* 0=+, 1=- */

        // turn minus into plus when x=y
        if (1 === $z && $y === $x) {
            $z = 0;
        }

        // turn minus into plus when y=v
        if (1 === $w && $v === $y) {
            $w = 0;
        }

        // make sure that x>y if z=1 (minus)
        if (1 === $z && $y > $x) {
            $tmp = $x;
            $x = $y;
            $y = $tmp;
        }

        // turn minus into plus when v>x-y or v>x+y
        if (1 === $w && (1 === $z && $v > ($x - $y)) || (0 === $z && $v > ($x + $y))) {
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
    private function hexToRgb($image, string $hexCode): int
    {
        sscanf($hexCode, "%2x%2x%2x", $red, $green, $blue);

        return imagecolorallocate($image, $red, $green, $blue);
    }
}
