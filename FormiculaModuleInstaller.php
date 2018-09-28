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
use Zikula\FormiculaModule\Entity\SubmissionEntity;

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
        'Zikula\FormiculaModule\Entity\SubmissionEntity',
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
            'excludeSpamCheck' => '',
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

            $isLegacy = version_compare(\Zikula_Core::VERSION_NUM, '2.0.0') >= 0 ? false : true;
            if ($isLegacy) {
                \EventUtil::unregisterPersistentModuleHandlers('Formicula');

                $conn->executeQuery("DELETE FROM $dbName.`hook_area` WHERE `owner` = 'Formicula'");
                $conn->executeQuery("DELETE FROM $dbName.`hook_binding` WHERE `sowner` = 'Formicula'");
                $conn->executeQuery("DELETE FROM $dbName.`hook_runtime` WHERE `sowner` = 'Formicula'");
                $conn->executeQuery("DELETE FROM $dbName.`hook_subscriber` WHERE `owner` = 'Formicula'");
            }

            // reinstall
            $this->install();

            $conn = $this->getConnection();
            $dbName = $this->getDbName();
            $hasMigrationData = false;

            // migrate old contacts
            $stmt = $conn->executeQuery("SELECT * FROM $dbName.`formcontacts`");
            while ($row = $stmt->fetch()) {
                $hasMigrationData = true;
                $contact = new ContactEntity();
                $contact->setCid($row['pn_cid']);
                $contact->setName($row['pn_name']);
                $contact->setEmail($row['pn_email']);
                $contact->setPublic((bool)$row['pn_public']);
                $contact->setSenderName($row['pn_sname']);
                $contact->setSenderEmail($row['pn_semail']);
                $contact->setSendingSubject($row['pn_ssubject']);
                $this->entityManager->persist($contact);
            }

            // migrate old submissions
            $stmt = $conn->executeQuery("SELECT * FROM $dbName.`formsubmits`");
            while ($row = $stmt->fetch()) {
                $hasMigrationData = true;
                $submission = new SubmissionEntity();
                $submission->setSid($row['pn_sid']);
                $submission->setForm($row['pn_form']);
                $submission->setCid($row['pn_cid']);
                $submission->setIpAddress($row['pn_ip']);
                $submission->setHostName($row['pn_host']);
                $submission->setName($row['pn_name']);
                $submission->setEmail($row['pn_email']);
                $submission->setPhoneNumber($row['pn_phone']);
                $submission->setCompany((bool)$row['pn_company']);
                $submission->setUrl($row['pn_url']);
                $submission->setLocation($row['pn_location']);
                $submission->setComment($row['pn_comment']);
                $customData = @unserialize($row['pn_customdata']);
                if ($customData) {
                    $submission->setCustomData($customData);
                }
                $this->entityManager->persist($submission);
            }

            // save migrated data
            if ($hasMigrationData) {
                $this->entityManager->flush();
            }

            $conn->executeQuery("DROP TABLE $dbName.`formcontacts`");
            $conn->executeQuery("DROP TABLE $dbName.`formsubmits`");

            $oldVersion = '4.0.0';
        }

        switch ($oldVersion) {
            case '4.0.0':
            case '4.0.1':
                // nothing to do
            case '4.0.2':
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
            if (!$fs->exists($cacheDirectory)) {
                $fs->mkdir($cacheDirectory);
                $fs->chmod($cacheDirectory, 0777);
            }
        } catch (IOExceptionInterface $e) {
            $this->addFlash('error', $this->__f('An error occurred while creating the cache directory at %s.', ['%s' => $e->getPath()]));
        }

        try {
            if ($fs->exists($cacheDirectory . '/.htaccess')) {
                return;
            }
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
            $this->addFlash('error', $this->__f('Could not create .htaccess file in %s, please refer to the manual before using the module!', ['%s' => $e->getPath()]));
        }
    }

    /**
     * Returns connection to the database.
     *
     * @return Connection the current connection
     */
    private function getConnection()
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
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
