<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module\SourceHandler;

use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
use PrestaShop\PrestaShop\Core\Security\SafeZipExtractor;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipArchive;

class ZipSourceHandler implements SourceHandlerInterface
{
    private const AUTHORIZED_MIME = [
        'application/zip',
        'application/x-gzip',
        'application/gzip',
        'application/x-gtar',
        'application/x-tgz',
    ];

    private const MODULE_REGEX = '/^([a-zA-Z0-9_-]+)\/\1\.php$/i'; // module_name/module_name.php

    private const EXTRACTION_DIRECTORY_ATTEMPTS = 5;

    /** @var string */
    protected $modulePath;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var string Folder where archives are extracted before the module is installed */
    protected $sandboxPath;

    /** @var SafeZipExtractor */
    protected $zipExtractor;

    /** @var Filesystem */
    protected $filesystem;

    public function __construct(
        string $modulePath,
        TranslatorInterface $translator,
        ?string $sandboxPath = null,
        ?SafeZipExtractor $zipExtractor = null,
        ?Filesystem $filesystem = null
    ) {
        $this->modulePath = rtrim($modulePath, '/') . '/';
        $this->translator = $translator;
        // Same sandbox as the rest of the shop when no path is given; the system temp dir only outside a shop
        $sandboxPath = $sandboxPath ?? (defined('_PS_CACHE_DIR_') ? _PS_CACHE_DIR_ . 'sandbox' : sys_get_temp_dir());
        $this->sandboxPath = rtrim($sandboxPath, '/') . '/';
        $this->zipExtractor = $zipExtractor ?? new SafeZipExtractor();
        $this->filesystem = $filesystem ?? new Filesystem();
    }

    public function canHandle($source): bool
    {
        return is_file($source) && in_array(mime_content_type($source), self::AUTHORIZED_MIME);
    }

    public function getModuleName($source): string
    {
        $zip = new ZipArchive();
        if ($zip->open($source) === true) {
            for ($i = 0; $i < $zip->numFiles; ++$i) {
                if (preg_match(self::MODULE_REGEX, $zip->getNameIndex($i), $matches)) {
                    $zip->close();

                    return $matches[1];
                }
            }
            $zip->close();
        }

        throw new ModuleErrorException(
            $this->translator->trans(
                'This file does not seem to be a valid module zip',
                [],
                'Admin.Modules.Notification'
            )
        );
    }

    /**
     * @throws ModuleErrorException
     */
    public function handle(string $source): void
    {
        $moduleName = $this->getModuleName($source);
        $extractionDirectory = null;

        try {
            $this->filesystem->mkdir($this->sandboxPath);
            $extractionDirectory = $this->createExtractionDirectory();
            if (null === $extractionDirectory) {
                throw $this->createCannotExtractException();
            }

            // The archive is checked and extracted aside, then only the module folder is installed:
            // nothing else from the archive reaches the modules directory
            if (!$this->zipExtractor->extract($source, $extractionDirectory)
                || !is_dir($extractionDirectory . DIRECTORY_SEPARATOR . $moduleName)) {
                throw new ModuleErrorException(
                    $this->translator->trans(
                        'This file does not seem to be a valid module zip',
                        [],
                        'Admin.Modules.Notification'
                    )
                );
            }

            $this->filesystem->mirror(
                $extractionDirectory . DIRECTORY_SEPARATOR . $moduleName,
                $this->modulePath . $moduleName,
                null,
                ['override' => true]
            );
        } catch (IOException $e) {
            // The message of a filesystem error holds the sandbox path, which is not shown to the merchant
            throw $this->createCannotExtractException($e);
        } finally {
            if (null !== $extractionDirectory) {
                try {
                    $this->filesystem->remove($extractionDirectory);
                } catch (IOException $e) {
                    // Left in the cache sandbox, removed with the cache
                }
            }
        }
    }

    /**
     * Error shown when the archive cannot be extracted: it gives the modules path, never the sandbox path.
     */
    private function createCannotExtractException(?IOException $previous = null): ModuleErrorException
    {
        return new ModuleErrorException(
            $this->translator->trans(
                'Cannot extract module in %path%. %error%',
                [
                    '%path%' => $this->modulePath,
                    '%error%' => '',
                ],
                'Admin.Modules.Notification'
            ),
            0,
            $previous
        );
    }

    /**
     * Creates an empty folder with a random name in the sandbox, or returns null when it cannot be created.
     * An existing path (folder, file or link) is never reused, another name is tried.
     */
    private function createExtractionDirectory(): ?string
    {
        for ($attempt = 0; $attempt < self::EXTRACTION_DIRECTORY_ATTEMPTS; ++$attempt) {
            $extractionDirectory = $this->sandboxPath . 'module_' . bin2hex(random_bytes(8));
            // exists() does not see a link whose target is missing, hence is_link()
            if ($this->filesystem->exists($extractionDirectory) || is_link($extractionDirectory)) {
                continue;
            }

            try {
                $this->filesystem->mkdir($extractionDirectory, 0700);
            } catch (IOException $e) {
                // The message of the exception holds the sandbox path, which is not shown to the merchant
                return null;
            }

            return $extractionDirectory;
        }

        return null;
    }
}
