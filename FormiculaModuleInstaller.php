<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula\FormiculaModule\Entity\ContactEntity;

/**
 * Installation routines for the Formicula module.
 */
class FormiculaModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var array
     */
    private $entities = [
        'Zikula\FormiculaModule\Entity\ContactEntity',
        'Zikula\FormiculaModule\Entity\SubmitEntity',
    ];

    /**
     * Initialise the Formicula module.
     *
     * @return boolean True if initialisation successful, false otherwise
     */
    public function install()
    {
        // create schema
        try {
            $this->schemaTool->create($this->entities);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }

        $variableApi = $this->container->get('zikula_extensions_module.api.variable');

        // create a contact for the webmaster
        $contact = new ContactEntity();
        $contact->setName($this->__('Webmaster'));
        $contact->setEmail($variableApi->get('ZConfig', 'adminmail'));
        $contact->setPublic(true);
        $contact->setSenderName($contact->getName());
        $contact->setSenderEmail($contact->getEmail());
        $contact->setSendingSubject($this->__('Your mail to %s'));
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        // try to create the cache directory
        $this->createCacheDirectory();

        $this->setVars([
            'defaultForm' => 0,
            'showCompany' => true,
            'showPhone' => true,
            'showUrl' => true,
            'showLocation' => true,
            'showComment' => true,

            'showFileAttachment' => false,
            'uploadDirectory' => 'userdata',
            'deleteUploadedFiles' => true,

            'sendConfirmationToUser' => true,
            'defaultAdminFormat' => 'html',
            'defaultUserFormat' => 'html',
            'showUserFormat' => true,
            'useContactsAsSender' => true,

            'enableSpamCheck' => true,
            'storeSubmissionData' => false,
            'storeSubmissionDataForms' => ''
        ]);

        // install subscriber hook
        $this->hookApi->installSubscriberHooks($this->bundle->getMetaData());

        // initialisation successful
        return true;
    }

    /**
     * Upgrade the module from an old version.
     *
     * @param string $oldVersion version number string to upgrade from
     *
     * @return bool|string true on success, last valid version string or false if fails
     */
    public function upgrade($oldVersion)
    {
        if (version_compare($oldVersion, '4.0.0', '<')) {
            // delete all old data
            $variableApi = $this->container->get('zikula_extensions_module.api.variable');
            $variableApi->delAll('formicula');
            $variableApi->delAll('Formicula');

            \EventUtil::unregisterPersistentModuleHandlers('Formicula');

            $conn = $this->getConnection();
            $dbName = $this->getDbName();

            $conn->executeQuery("DELETE FROM $dbName.hook_area WHERE `owner` = 'Formicula'");
            $conn->executeQuery("DELETE FROM $dbName.hook_binding WHERE `owner` = 'Formicula'");
            $conn->executeQuery("DELETE FROM $dbName.hook_runtime WHERE `sowner` = 'Formicula'");
            $conn->executeQuery("DELETE FROM $dbName.hook_subscriber WHERE `owner` = 'Formicula'");
            $conn->executeQuery("DROP TABLE $dbName.formcontacts");
            $conn->executeQuery("DROP TABLE $dbName.formsubmits");

            // reinstall
            $this->install();

            $oldVersion = '4.0.0';
        }

        switch ($oldVersion) {
            case '4.0.0':
                // future upgrades
        }

        // Update successful
        return true;
    }

    /**
     * Delete the Formicula module.
     *
     * @return bool true if deletion successful, false otherwise
     */
    public function uninstall()
    {
        try {
            $this->schemaTool->drop($this->entities);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return false;
        }

        // Delete any module variables
        $this->delVars();

        $cacheDirectory = $this->getCacheDirectory();
        if (is_dir($cacheDirectory)) {
            $fs = new Filesystem();
            try {
                $fs->remove($cacheDirectory);
            } catch (IOExceptionInterface $e) {
                $this->addFlash('error', $this->__f('An error occurred while removing the cache directory at %s.', ['%s' => $e->getPath()]));
            }
        }

        // Remove module variables
        $this->delVars();

        // uninstall subscriber hook
        $this->hookApi->uninstallSubscriberHooks($this->bundle->getMetaData());

        // Deletion successful
        return true;
    }

    /**
     * Returns path to cache directory.
     *
     * @return string Path to temporary cache directory
     */
    private function getCacheDirectory()
    {
        return 'app/cache/formicula';
    }

    /**
     * Creates the cache directory.
     *
     * @return void
     */
    private function createCacheDirectory()
    {
        $cacheDirectory = $this->getCacheDirectory();
        $fs = new Filesystem();
        try {
            $fs->mkdir($cacheDirectory);
            $fs->chmod($cacheDirectory, 0777);
        } catch (IOExceptionInterface $e) {
            $this->addFlash('error', $this->__f('An error occurred while creating the cache directory at %s.', ['%s' => $e->getPath()]));
        }

        try {
            $fs->dumpFile($cacheDirectory . '/.htaccess', 'SetEnvIf Request_URI "\.gif$" object_is_gif=gif
SetEnvIf Request_URI "\.png$" object_is_png=png
SetEnvIf Request_URI "\.jpg$" object_is_jpg=jpg
SetEnvIf Request_URI "\.jpeg$" object_is_jpeg=jpeg
Order deny,allow
Deny from all
Allow from env=object_is_gif
Allow from env=object_is_png
Allow from env=object_is_jpg
Allow from env=object_is_jpeg
');
            $this->addFlash('status', $this->__('Successfully created the cache directory with a .htaccess file in it.'));
        } catch (IOExceptionInterface $e) {
            $this->addFlash('error', $this->__f('Could not create .htaccess file in %s, please refer to the manual before using the module!', ['%s': $e->getPath()]));
        }
    }

    /**
     * Returns connection to the database.
     *
     * @return Connection the current connection
     */
    private function getConnection()
    {
        $entityManager = $this->container->get('doctrine.entitymanager');
        $connection = $entityManager->getConnection();

        return $connection;
    }
    /**
     * Returns the name of the default system database.
     *
     * @return string the database name
     */
    private function getDbName()
    {
        return $this->container->getParameter('database_name');
    }
}
