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

namespace Zikula\FormiculaModule;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Doctrine\Helper\SchemaHelper;
use Zikula\ExtensionsModule\AbstractExtension;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ExtensionsModule\Installer\AbstractExtensionInstaller;
use Zikula\FormiculaModule\Entity\ContactEntity;
use Zikula\FormiculaModule\Entity\SubmissionEntity;

class FormiculaModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var array
     */
    private $entities = [
        ContactEntity::class,
        SubmissionEntity::class
    ];

    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var string
     */
    private $uploadDirectory;

    public function __construct(
        AbstractExtension $extension,
        ManagerRegistry $managerRegistry,
        SchemaHelper $schemaTool,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        VariableApiInterface $variableApi,
        string $cacheDir
    ) {
        parent::__construct($extension, $managerRegistry, $schemaTool, $requestStack, $translator, $variableApi);
        $this->cacheDirectory = $cacheDir;
        $this->uploadDirectory = str_replace('cache', 'uploads', $cacheDir);
    }

    public function install(): bool
    {
        $this->schemaTool->create($this->entities);

        $variableApi = $this->getVariableApi();

        // create a contact for the webmaster
        $contact = new ContactEntity();
        $contact->setName($this->trans('Webmaster'));
        $contact->setEmail($variableApi->get('ZConfig', 'adminmail'));
        $contact->setPublic(true);
        $contact->setSenderName($contact->getName());
        $contact->setSenderEmail($contact->getEmail());
        $contact->setSendingSubject($this->trans('Your mail to %s'));
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        // try to create required directories
        $this->createCacheDirectory();
        $this->createUploadDirectory();

        $this->setVars($this->getDefaultSettings());

        // initialisation successful
        return true;
    }

    public function upgrade($oldVersion): bool
    {
        switch ($oldVersion) {
            case '4.0.0':
            case '4.0.1':
                // nothing to do
            case '4.0.2':
                // fields have changed: createdUserId becomes createdBy, updatedUserId becomes updatedBy
                $connection = $this->entityManager->getConnection();
                $tableName = 'submission';
                $sql = '
                    ALTER TABLE `formicula_' . $tableName . '`
                    CHANGE `createdUserId` `createdBy` INT(11) DEFAULT NULL
                ';
                $stmt = $connection->prepare($sql);
                $stmt->execute();

                $sql = '
                    ALTER TABLE `formicula_' . $tableName . '`
                    CHANGE `updatedUserId` `updatedBy` INT(11) DEFAULT NULL
                ';
                $stmt = $connection->prepare($sql);
                $stmt->execute();
            case '5.0.0':
                // added forgotten company field
                try {
                    $this->schemaTool->update([
                        SubmissionEntity::class
                    ]);
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->trans('Doctrine Exception') . ': ' . $exception->getMessage());

                    return false;
                }
            case '5.0.1':
                $this->setVar('uploadDirectory', $this->uploadDirectory);
            case '5.0.2':
                // nothing yet
        }

        // Update successful
        return true;
    }

    public function uninstall(): bool
    {
        $this->schemaTool->drop($this->entities);

        $this->delVars();

        if (is_dir($this->cacheDirectory)) {
            $fs = new Filesystem();
            try {
                $fs->remove($this->cacheDirectory);
            } catch (IOExceptionInterface $exception) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'An error occurred while removing the cache directory at %s%.',
                        ['%s%' => $this->cacheDirectory]
                    )
                );
            }
        }

        // upload directory is currently not deleted, since the files may still be of interest

        return true;
    }

    private function createCacheDirectory(): void
    {
        $fs = new Filesystem();
        try {
            if (!$fs->exists($this->cacheDirectory)) {
                $fs->mkdir($this->cacheDirectory);
                $fs->chmod($this->cacheDirectory, 0777);
            }
        } catch (IOExceptionInterface $exception) {
            $this->addFlash(
                'error',
                $this->trans(
                    'An error occurred while creating the cache directory at %s%.',
                    ['%s%' => $this->cacheDirectory]
                )
            );
        }

        try {
            if ($fs->exists($this->cacheDirectory . '/.htaccess')) {
                return;
            }
            $fs->dumpFile($this->cacheDirectory . '/.htaccess', 'SetEnvIf Request_URI "\.gif$" object_is_gif=gif
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
            $this->addFlash(
                'status',
                $this->trans('Successfully created the cache directory with a .htaccess file in it.')
            );
        } catch (IOExceptionInterface $exception) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Could not create .htaccess file in %s%, please refer to the manual before using the module!',
                    ['%s%' => $this->cacheDirectory]
                )
            );
        }
    }

    private function createUploadDirectory(): void
    {
        $fs = new Filesystem();
        try {
            if (!$fs->exists($this->uploadDirectory)) {
                $fs->mkdir($this->uploadDirectory);
                $fs->chmod($this->uploadDirectory, 0777);
            }
        } catch (IOExceptionInterface $exception) {
            $this->addFlash(
                'error',
                $this->trans(
                    'An error occurred while creating the upload directory at %s%.',
                    ['%s%' => $this->uploadDirectory]
                )
            );
        }
    }

    private function getDefaultSettings(): array
    {
        return [
            'defaultForm' => 0,
            'showCompany' => true,
            'showPhone' => true,
            'showUrl' => true,
            'showLocation' => true,
            'showComment' => true,

            'showFileAttachment' => false,
            'uploadDirectory' => $this->uploadDirectory,
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
        ];
    }
}
