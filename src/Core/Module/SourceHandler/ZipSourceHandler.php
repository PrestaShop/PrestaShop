<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module\SourceHandler;

use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
use PrestaShop\PrestaShop\Core\Security\Exception\ZipExtractionException;
use PrestaShop\PrestaShop\Core\Security\SafeZipExtractor;
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

    /** @var string */
    protected $modulePath;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var SafeZipExtractor */
    protected $zipExtractor;

    public function __construct(string $modulePath, TranslatorInterface $translator, ?SafeZipExtractor $zipExtractor = null)
    {
        $this->modulePath = rtrim($modulePath, '/') . '/';
        $this->translator = $translator;
        $this->zipExtractor = $zipExtractor ?? new SafeZipExtractor();
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

        try {
            // Only the module folder is installed: nothing else from the archive reaches the modules directory
            $this->zipExtractor->extract($source, $this->modulePath, $moduleName);
        } catch (ZipExtractionException $e) {
            if ($e->isInvalidArchive()) {
                throw new ModuleErrorException(
                    $this->translator->trans(
                        'This file does not seem to be a valid module zip',
                        [],
                        'Admin.Modules.Notification'
                    ),
                    0,
                    $e
                );
            }

            throw new ModuleErrorException(
                $this->translator->trans(
                    'Cannot extract module in %path%. %error%',
                    [
                        '%path%' => $this->modulePath,
                        '%error%' => '',
                    ],
                    'Admin.Modules.Notification'
                ),
                0,
                $e
            );
        }
    }
}
