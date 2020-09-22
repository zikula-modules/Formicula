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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Zikula\ExtensionsModule\AbstractExtension;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\FormiculaModule\Form\Type\ConfigType;
use Zikula\FormiculaModule\Helper\EnvironmentHelper;
use Zikula\PermissionsModule\Annotation\PermissionCheck;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * @Route("/config")
 * @PermissionCheck("ACCESS_ADMIN")
 */
class ConfigController extends AbstractController
{
    /**
     * @var EnvironmentHelper
     */
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
    public function config(Request $request, string $cacheDir): array
    {
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
            $label = $this->trans(
                'Form #%num% containing %count% templates',
                [
                    '%num%' => $formNumber,
                    '%count%' => count($finder2)
                ]
            );
            $formChoices[$label] = $formNumber;
        }

        $form = $this->createForm(
            ConfigType::class,
            $modVars,
            [
                'formChoices' => $formChoices
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('save')->isClicked()) {
                $formData = $form->getData();

                if (!empty($formData['uploadDirectory']) && !is_writable($formData['uploadDirectory'])) {
                    $this->addFlash('error', 'The webserver cannot write into the upload directory!');
                } else {
                    // remove spaces in the comma separated forms lists
                    $formData['excludeSpamCheck'] = preg_replace('/\s*/m', '', $formData['excludeSpamCheck']);
                    $formData['storeSubmissionDataForms'] = preg_replace('/\s*/m', '', $formData['storeSubmissionDataForms']);

                    $this->setVars($formData);

                    $this->addFlash('status', 'Done! Configuration updated.');
                }
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', 'Operation cancelled.');
            }
        }

        $templateParameters = array_merge($modVars, [
            'form' => $form->createView(),
            'cacheDirectory' => $cacheDir
        ]);

        return $templateParameters;
    }

    /**
     * Clear image cache.
     *
     * @Route("/clearcache")
     * @Theme("admin")
     */
    public function clearCache(): RedirectResponse
    {
        $this->environmentHelper->clearCache();
        $this->addFlash('status', 'The captcha image cache has been cleared.');

        return $this->redirectToRoute('zikulaformiculamodule_contact_view');
    }
}
