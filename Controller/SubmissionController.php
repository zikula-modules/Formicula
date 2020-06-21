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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Zikula\ExtensionsModule\AbstractExtension;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\FormiculaModule\Entity\Repository\SubmissionRepository;
use Zikula\FormiculaModule\Form\Type\DeleteSubmissionType;
use Zikula\FormiculaModule\Helper\EnvironmentHelper;
use Zikula\PermissionsModule\Annotation\PermissionCheck;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * @Route("/submission")
 */
class SubmissionController extends AbstractController
{
    /**
     * @var SubmissionRepository
     */
    private $submissionRepository;

    /**
     * @var EnvironmentHelper
     */
    private $environmentHelper;

    public function __construct(
        AbstractExtension $extension,
        PermissionApiInterface $permissionApi,
        VariableApiInterface $variableApi,
        TranslatorInterface $translator,
        SubmissionRepository $submissionRepository,
        EnvironmentHelper $environmentHelper
    ) {
        parent::__construct($extension, $permissionApi, $variableApi, $translator);
        $this->submissionRepository = $submissionRepository;
        $this->environmentHelper = $environmentHelper;
    }

    /**
     * Shows a list of submissions.
     *
     * @Route("/view")
     * @PermissionCheck("admin")
     * @Template("@ZikulaFormiculaModule/Submission/view.html.twig")
     * @Theme("admin")
     */
    public function viewAction(Request $request)
    {
        // check necessary environment
        $this->environmentHelper->check();

        $submissions = $this->submissionRepository->findBy([], ['sid' => 'DESC']);

        return [
            'submissions' => $submissions
        ];
    }

    /**
     * Shows a specific form submission.
     *
     * @Route("/display")
     * @PermissionCheck("admin")
     * @Template("@ZikulaFormiculaModule/Submission/display.html.twig")
     * @Theme("admin")
     */
    public function displayAction(Request $request)
    {
        // check necessary environment
        $this->environmentHelper->check();

        $submissionId = $request->query->getDigits('sid', -1);
        $submission = $this->submissionRepository->find($submissionId);
        if (false === $submission) {
            $this->addFlash('error', 'Form submission could not be found.');

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
     * @PermissionCheck("delete")
     * @Template("@ZikulaFormiculaModule/Submission/delete.html.twig")
     * @Theme("admin")
     */
    public function deleteAction(Request $request)
    {
        // check necessary environment
        $this->environmentHelper->check();

        $entityManager = $this->get('doctrine')->getManager();
        $submissionId = $request->query->getDigits('sid', -1);

        $submission = $this->submissionRepository->find($submissionId);
        if (false === $submission) {
            $this->addFlash('error', 'Form submission could not be found.');

            return $this->redirectToRoute('zikulaformiculamodule_submission_view');
        }

        $form = $this->createForm(DeleteSubmissionType::class, $submission);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $submission = $form->getData();
                $entityManager->remove($submission);
                $entityManager->flush();
                $this->addFlash('status', 'Done! Submission deleted.');
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', 'Operation cancelled.');
            }

            return $this->redirectToRoute('zikulaformiculamodule_submission_view');
        }

        return [
            'form' => $form->createView(),
            'submission' => $submission
        ];
    }
}
