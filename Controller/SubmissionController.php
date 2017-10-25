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
     * @Template("ZikulaFormiculaModule:Submission:view.html.twig")
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
        $this->get('zikula_formicula_module.helper.environment_helper')->check();

        $submissions = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\SubmissionEntity')->findBy([], ['sid' => 'DESC']);

        return [
            'submissions' => $submissions
        ];
    }

    /**
     * Shows a specific form submission.
     *
     * @Route("/display")
     * @Theme("admin")
     * @Template("ZikulaFormiculaModule:Submission:display.html.twig")
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
        $this->get('zikula_formicula_module.helper.environment_helper')->check();

        $submissionId = $request->query->getDigits('sid', -1);
        $submission = $this->get('doctrine')->getManager()->getRepository('Zikula\FormiculaModule\Entity\SubmissionEntity')->find($submissionId);
        if (false === $submission) {
            $this->addFlash('error', $this->__('Form submission could not be found.'));

            return $this->redirectToRoute('zikulaformiculamodule_submission_view');
        }

        return [
            'submission' => $submission
        ];
    }

    /**
     * Deletes an existing submit from the database.
     *
     * @Route("/delete")
     * @Theme("admin")
     * @Template("ZikulaFormiculaModule:Submission:delete.html.twig")
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
        $this->get('zikula_formicula_module.helper.environment_helper')->check();

        $entityManager = $this->get('doctrine')->getManager();
        $submissionId = $request->query->getDigits('sid', -1);

        $submission = $entityManager->getRepository('Zikula\FormiculaModule\Entity\SubmissionEntity')->find($submissionId);
        if (false === $submission) {
            $this->addFlash('error', $this->__('Form submission could not be found.'));

            return $this->redirectToRoute('zikulaformiculamodule_submission_view');
        }

        $form = $this->createForm('Zikula\FormiculaModule\Form\Type\DeleteSubmissionType', $submission, [
            'translator' => $this->get('translator.default')
        ]);

        if ($form->handleRequest($request)->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $submission = $form->getData();
                $entityManager->remove($submission);
                $entityManager->flush();
                $this->addFlash('status', $this->__('Done! Submission deleted.'));
            }
            if ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->__('Operation cancelled.'));
            }

            return $this->redirectToRoute('zikulaformiculamodule_submission_view');
        }

        return [
            'form' => $form->createView(),
            'submission' => $submission
        ];
    }
}
