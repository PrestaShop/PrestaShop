<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module\SourceHandler;

use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
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

    private const MODULE_REGEX = '/^(.*)\/\1\.php$/i'; // module_name/module_name.php

    /** @var string */
    protected $modulePath;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(string $modulePath, TranslatorInterface $translator)
    {
        $this->modulePath = rtrim($modulePath, '/') . '/';
        $this->translator = $translator;
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

    public function handle(string $source): void
    {
        $zip = new ZipArchive();
        if ($zip->open($source) !== true) {
            throw new ModuleErrorException(
                $this->translator->trans(
                    'Cannot extract module in %path%. %error%',
                    [
                        '%path%' => $this->modulePath,
                        '%error%' => '',
                    ],
                    'Admin.Modules.Notification'
                )
            );
        }

        // Validate all entry paths before extraction to prevent Zip Slip path traversal.
        // An attacker-controlled ZIP could contain entries like ../../config/config.inc.php
        // that would escape the module directory when extracted with extractTo().
        $realModulePath = realpath($this->modulePath);
        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $entryName = $zip->getNameIndex($i);
            // Resolve the target path and verify it stays within the module directory.
            $entryPath = realpath($this->modulePath . $entryName);
            if ($entryPath === false) {
                // realpath() returns false for paths whose parent does not yet exist;
                // normalise manually to catch traversal sequences.
                $entryPath = $this->normalizePath($this->modulePath . $entryName);
            }
            if ($realModulePath === false || !str_starts_with($entryPath . DIRECTORY_SEPARATOR, $realModulePath . DIRECTORY_SEPARATOR)) {
                $zip->close();
                throw new ModuleErrorException(
                    $this->translator->trans(
                        'Illegal path in module archive: %path%',
                        ['%path%' => $entryName],
                        'Admin.Modules.Notification'
                    )
                );
            }
        }

        if (!$zip->extractTo($this->modulePath) || !$zip->close()) {
            throw new ModuleErrorException(
                $this->translator->trans(
                    'Cannot extract module in %path%. %error%',
                    [
                        '%path%' => $this->modulePath,
                        '%error%' => @$zip->getStatusString() ?: '',
                    ],
                    'Admin.Modules.Notification'
                )
            );
        }
    }

    /**
     * Normalises a path by resolving . and .. segments without requiring the
     * path to exist on disk (unlike realpath()).
     */
    private function normalizePath(string $path): string
    {
        $parts = [];
        foreach (explode(DIRECTORY_SEPARATOR, str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path)) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($parts);
            } else {
                $parts[] = $segment;
            }
        }

        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
    }
}
