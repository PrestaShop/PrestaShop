<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module\SourceHandler;

use Phar;
use PharData;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
use RecursiveIteratorIterator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class TarSourceHandler implements SourceHandlerInterface
{
    private const AUTHORIZED_MIME = [
        'application/gzip',
        'application/x-gzip',
        'application/x-gtar',
        'application/x-tgz',
        'application/x-tar',
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
        return is_file($source) && in_array(mime_content_type($source), self::AUTHORIZED_MIME, true);
    }

    public function getModuleName($source): string
    {
        $archivePath = $this->copyToNamedArchive($source);

        try {
            $archive = new PharData($archivePath);
            $iterator = new RecursiveIteratorIterator($archive);
            foreach ($iterator as $file) {
                if (preg_match(self::MODULE_REGEX, $iterator->getSubPathName(), $matches)) {
                    return $matches[1];
                }
            }
        } catch (Throwable $e) {
            // Not a readable archive; fall through to the exception below.
        } finally {
            $this->removeNamedArchive($archivePath);
        }

        throw new ModuleErrorException(
            $this->translator->trans(
                'This file does not seem to be a valid module archive',
                [],
                'Admin.Modules.Notification'
            )
        );
    }

    public function handle(string $source): void
    {
        $archivePath = $this->copyToNamedArchive($source);

        try {
            $archive = new PharData($archivePath);
            $archive->extractTo($this->modulePath, null, true);
        } catch (Throwable $e) {
            throw new ModuleErrorException(
                $this->translator->trans(
                    'Cannot extract module in %path%. %error%',
                    [
                        '%path%' => $this->modulePath,
                        '%error%' => $e->getMessage(),
                    ],
                    'Admin.Modules.Notification'
                )
            );
        } finally {
            $this->removeNamedArchive($archivePath);
        }
    }

    /**
     * PharData detects the archive format from the file name extension, but uploaded files are
     * stored under an extension-less temporary name. Copy the source to a temporary file that
     * keeps the .tar.gz extension so PharData can open it.
     *
     * @throws ModuleErrorException
     */
    private function copyToNamedArchive(string $source): string
    {
        $archivePath = sprintf('%s/%s.tar.gz', sys_get_temp_dir(), uniqid('ps_module_', true));

        if (!is_file($source) || !copy($source, $archivePath)) {
            throw new ModuleErrorException(
                $this->translator->trans(
                    'Cannot read the uploaded module archive.',
                    [],
                    'Admin.Modules.Notification'
                )
            );
        }

        return $archivePath;
    }

    private function removeNamedArchive(string $archivePath): void
    {
        if (!is_file($archivePath)) {
            return;
        }

        try {
            // PharData keeps the archive open; this releases it and removes the temporary file.
            Phar::unlinkArchive($archivePath);
        } catch (Throwable $e) {
            @unlink($archivePath);
        }
    }
}
