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

namespace Zikula\FormiculaModule\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Zikula\ExtensionsModule\AbstractExtension;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\FormiculaModule\Form\Type\ConfigType;
use Zikula\FormiculaModule\Helper\EnvironmentHelper;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Class ConfigController
 * @Route("/config")
 */
class ConfigController extends AbstractController
{
    private $environmentHelper;

    public function __construct(
        AbstractExtension $extension,
        PermissionApiInterface $permissionApi,
        VariableApiInterface $variableApi,
        TranslatorInterface $translator,
        EnvironmentHelper $environmentHelper
    ) {
        parent::__construct($extension, $permissionApi, $variableApi, $translator);
        $this->environmentHelper = $environmentHelper;
    }

    /**
     * @Route("/config")
     * @Template("@ZikulaFormiculaModule/Config/config.html.twig")
     * @Theme("admin")
     */
    public function configAction(
        VariableApiInterface $variableApi,
        Request $request
    ) {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $this->environmentHelper->check();

        $modVars = $this->getVars();

        // scan the templates folder for installed forms
        $templateDirectory = __DIR__ . '/../Resources/views/Form/';
        $formChoices = [];
        $finder = new Finder();

        foreach ($finder->directories()->in($templateDirectory)->sortByName() as $directory) {
            $finder2 = new Finder();
            $finder2->files()->in($directory->getRealPath());
            $formNumber = $directory->getFilename();
            $formChoices[$this->trans('Form #%num% containing %count% templates', ['%num%' => $formNumber, '%count%' => count($finder2)])] = $formNumber;
        }

        $form = $this->createForm(
            ConfigType::class,
            $modVars,
            [
                'formChoices' => $formChoices
            ]
        );

        if ($form->handleRequest($request)->isValid()) {
            if ($form->get('save')->isClicked()) {
                $formData = $form->getData();

                if (!empty($formData['uploadDirectory']) && !is_writable($formData['uploadDirectory'])) {
                    $this->addFlash('error', $this->trans('The webserver cannot write into this directory!'));
                } else {
                    // remove spaces in the comma separated forms lists
                    $formData['excludeSpamCheck'] = preg_replace('/\s*/m', '', $formData['excludeSpamCheck']);
                    $formData['storeSubmissionDataForms'] = preg_replace('/\s*/m', '', $formData['storeSubmissionDataForms']);

                    $this->setVars($formData);

                    $this->addFlash('status', $this->trans('Done! Module configuration updated.'));
                }
            }
            if ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->trans('Operation cancelled.'));
            }
        }

        $templateParameters = array_merge($modVars, [
            'form' => $form->createView(),
            'cacheDirectory' => $this->environmentHelper->getCacheDirectory()
        ]);

        return $templateParameters;
    }

    /**
     * Clear image cache.
     *
     * @Route("/clearcache")
     * @Theme("admin")
     */
    public function clearcacheAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $cacheDirectory = $this->environmentHelper->getCacheDirectory();
        $finder = new Finder();
        foreach ($finder->files()->in($cacheDirectory) as $file) {
            $fileName = $file->getFilename();
            if (in_array($fileName, ['.htaccess', 'index.htm', 'index.html'])) {
                continue;
            }
            unlink($file->getRealPath());
        }

        $this->addFlash('status', $this->trans('The captcha image cache has been cleared.'));

        return $this->redirectToRoute('zikulaformiculamodule_contact_view');
    }
}
