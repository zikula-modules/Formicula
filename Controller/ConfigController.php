<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Class ConfigController
 * @Route("/config")
 */
class ConfigController extends AbstractController
{
    /**
     * @Route("/config")
     * @Theme("admin")
     * @Template
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function configAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $environmentChecker = $this->get('zikula_formicula_module.helper.environment_checker');
        $environmentChecker->check();

        $variableApi = $this->get('zikula_extensions_module.api.variable');
        $modVars = $variableApi->getAll('ZikulaFormiculaModule');

        // scan the templates folder for installed forms
        // TODO determine actual module folder dynamically
        $templateDirectory = 'modules/Zikula/FormiculaModule/Resources/views/Form/';
        $formChoices = []
        $finder = new Finder();
        $finder2 = new Finder();

        foreach ($finder->directories()->in($templateFolder) as $directory) {
            $finder2->files()->in($directory->getRealPath());
            $formNumber = $directory->getFilename();
            $formChoices[$this->__f('Form #%1$s containing %2$s templates', [ '%1$s': $formNumber, '%2$s': count($finder2) ])] = $formNumber;
        }

        $form = $this->createForm('Zikula\FormiculaModule\Form\Type\ConfigType',
            $modVars, [
                'translator' => $this->get('translator.default'),
                'formChoices' => $formChoices
            ]
        );

        if ($form->handleRequest($request)->isValid()) {
            if ($form->get('save')->isClicked()) {
                $formData = $form->getData();

                if (!empty($formData['uploadDirectory']) && !is_writable($formData['uploadDirectory'])) {
                    $this->addFlash('error', $this->__('The webserver cannot write into this directory!'));
                } else {
                    // remove spaces in the comma separated forms lists
                    $formData['excludeSpamCheck'] = preg_replace('/\s*/m', '', $formData['excludeSpamCheck']);
                    $formData['storeSubmitDataForms'] = preg_replace('/\s*/m', '', $formData['storeSubmitDataForms']);

                    $this->setVars($formData);

                    $this->addFlash('status', $this->__('Done! Module configuration updated.'));
                }
            }
            if ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->__('Operation cancelled.'));
            }
        }

        $templateParameters = array_merge($modVars, [
            'form' => $form->createView(),
            'cacheDirectory' => $environmentChecker->getCacheDirectory()
        ]);

        return $templateParameters;
    }

    /**
     * Clear image cache.
     *
     * @Route("/clearcache")
     * @Theme("admin")
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return RedirectResponse
     */
    public function clearcacheAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        $cacheDirectory = $this->get('zikula_formicula_module.helper.environment_checker')->getCacheDirectory();
        $finder = new Finder();
        foreach ($finder->files()->in($cacheDirectory) as $file) {
            $fileName = $file->getFilename();
            if (in_array($file, ['.htaccess', 'index.htm', 'index.html'])) {
                continue;
            }
            unlink($file->getRealPath());
        }

        $this->addFlash('status', $this->__('The captcha image cache has been cleared.'));

        return $this->redirectToRoute('zikulaformiculamodule_contact_view');
    }
}
