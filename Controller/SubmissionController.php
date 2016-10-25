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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Class SubmissionController
 * @Route("/submission")
 */
class SubmissionController extends AbstractController
{
    /**
     * Shows a list of submissions.
     *
     * @Route("/view")
     * @Theme("admin")
     * @Template
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function viewAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $this->get('zikula_formicula_module.helper.environment_checker')->check();

        $submissions = ModUtil::apiFunc('Formicula', 'admin', 'getFormSubmits');

        return [
            'submissions' => $submissions
        ];
    }

    /**
     * Shows a specific form submission.
     *
     * @Route("/display")
     * @Theme("admin")
     * @Template
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function displayAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $this->get('zikula_formicula_module.helper.environment_checker')->check();

        $submissionId = $request->query->getDigits('sid', -1);

        $submission = ModUtil::apiFunc('Formicula', 'admin', 'getFormSubmit', ['sid' => $submissionId]);

        return [
            'submission' => $submission
        ];
    }

    /**
     * Deletes an existing submit from the database.
     *
     * @Route("/delete")
     * @Theme("admin")
     * @Template
     *
     * @param Request $request
     * @throws AccessDeniedException Thrown if the user doesn't have admin access to the module
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        // Security check
        if (!$this->hasPermission('ZikulaFormiculaModule::', '::', ACCESS_DELETE)) {
            throw new AccessDeniedException();
        }

        // check necessary environment
        $this->get('zikula_formicula_module.helper.environment_checker')->check();

        $submissionId = $request->query->getDigits('sid', -1);
        $confirmation = $request->request->get('confirmation', '');

        $submit = ModUtil::apiFunc('Formicula', 'admin', 'getFormSubmit', ['sid' => $submissionId]);
        if (false === $submit) {
            $this->addFlash('error', $this->__('Unknown form submission'));

            return $this->redirectToRoute('zikulaformiculamodule_submission_view');
        }

        // Check for confirmation.
        if (!empty($confirmation)) {
            // Confirm security token code
            $this->checkCsrfToken();        

            if (ModUtil::apiFunc('Formicula', 'admin', 'deleteSubmit', ['sid' => $submissionId])) {
                // Success
                $this->addFlash('status', $this->__('Form submit has been deleted'));
            }

            return $this->redirectToRoute('zikulaformiculamodule_submission_view');
        }

        return [
            'submission' => $submit
        ];
    }
}
