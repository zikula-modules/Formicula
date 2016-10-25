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

use Zikula\Core\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * Show a list of contacts.
     *
     * @return view output
     */
    public function view()
    {
        @trigger_error('This method is deprecated. please use the zikulaformiculamodule_contact_view route instead.', E_USER_DEPRECATED);

        return $this->redirectToRoute('zikulaformiculamodule_contact_view');
    }

    /**
     * Allows adding and editing contacts.
     *
     * @param cid int contact id, -1 for new contacts
     * @return view output
     */
    public function edit()
    {
        @trigger_error('This method is deprecated. please use the zikulaformiculamodule_contact_edit route instead.', E_USER_DEPRECATED);

        return $this->redirectToRoute('zikulaformiculamodule_contact_edit');
    }

    /**
     * Deletes an existing contact from the database.
     *
     * @param cid int contact id
     * @param confirmation string any value
     * @return view output on error or forwards to view()
     */
    public function delete()
    {
        @trigger_error('This method is deprecated. please use the zikulaformiculamodule_contact_delete route instead.', E_USER_DEPRECATED);

        return $this->redirectToRoute('zikulaformiculamodule_contact_delete');
    }

    /**
     * Shows a list of submissions.
     *
     * @return view output
     */
    public function viewsubmits()
    {
        @trigger_error('This method is deprecated. please use the zikulaformiculamodule_submission_view route instead.', E_USER_DEPRECATED);

        return $this->redirectToRoute('zikulaformiculamodule_submission_view');
    }

    /**
     * Shows a specific form submission.
     *
     * @param sid int formsubmit id
     * @return view output
     */
    public function displaysubmit()
    {
        @trigger_error('This method is deprecated. please use the zikulaformiculamodule_submission_display route instead.', E_USER_DEPRECATED);

        return $this->redirectToRoute('zikulaformiculamodule_submission_display');
    }

    /**
     * Deletes an existing submit from the database.
     *
     * @param cid int contact id
     * @param confirmation string any value
     * @return view output on error or forwards to view()
     */
    public function deletesubmit()
    {
        @trigger_error('This method is deprecated. please use the zikulaformiculamodule_submission_delete route instead.', E_USER_DEPRECATED);

        return $this->redirectToRoute('zikulaformiculamodule_submission_delete');
    }

    /**
     * modifyconfig
     * main entry point for configuration of module behaviour
     *
     * @return view output
     */
    public function modifyconfig()
    {
        @trigger_error('This method is deprecated. please use the zikulaformiculamodule_config_config route instead.', E_USER_DEPRECATED);

        return $this->redirectToRoute('zikulaformiculamodule_config_config');
    }

    /**
     * clear image cache
     */
    public function clearcache()
    {
        @trigger_error('This method is deprecated. please use the zikulaformiculamodule_config_clearcache route instead.', E_USER_DEPRECATED);

        return $this->redirectToRoute('zikulaformiculamodule_config_clearcache');
    }
}
