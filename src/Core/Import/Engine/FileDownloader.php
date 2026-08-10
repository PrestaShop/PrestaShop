<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\Exception\FileDownloadException;

/**
 * Fetches a file referenced by import data (http/https URL or local path)
 * into a local temporary file, and nothing more: association, validation,
 * resizing and thumbnail generation belong to the CQRS commands the importer
 * dispatches with the returned path. Used for product images and virtual
 * product files (file_url); replaces the download half of the deprecated
 * ImageCopier.
 */
class FileDownloader
{
    protected const DOWNLOAD_TIMEOUT_SECONDS = 20;

    /**
     * Hard cap on the fetched size, whatever the source claims: without it a
     * single file_url row could exhaust the disk. 128 MB, because virtual
     * product files (the largest legitimate use) can be big — images are
     * re-validated by the image commands anyway.
     */
    protected const MAX_FILE_SIZE_BYTES = 128 * 1024 * 1024;

    /**
     * @return string path of the temporary file — the caller is responsible for deleting it
     *
     * @throws FileDownloadException
     */
    public function download(string $urlOrPath): string
    {
        $scheme = parse_url($urlOrPath, PHP_URL_SCHEME);

        if (in_array($scheme, ['http', 'https'], true)) {
            return $this->downloadUrl($urlOrPath);
        }

        if (null !== $scheme && 1 !== preg_match('/^[a-z]$/i', (string) $scheme)) {
            // any other scheme (ftp://, file://, phar://, ...) is refused; a
            // single letter is a Windows drive, not a scheme
            throw new FileDownloadException(sprintf('Unsupported URL scheme in "%s"', $urlOrPath));
        }

        return $this->copyLocalFile($urlOrPath);
    }

    protected function downloadUrl(string $url): string
    {
        $sanitizedUrl = $this->sanitizeUrl($url);
        $targetPath = $this->createTemporaryFile();

        $context = stream_context_create([
            'http' => [
                'timeout' => self::DOWNLOAD_TIMEOUT_SECONDS,
                'follow_location' => 1,
                'max_redirects' => 3,
            ],
        ]);

        $source = @fopen($sanitizedUrl, 'rb', false, $context);
        if (false === $source) {
            @unlink($targetPath);
            throw new FileDownloadException(sprintf('Could not download "%s"', $url));
        }

        $target = fopen($targetPath, 'wb');
        if (false === $target) {
            fclose($source);
            @unlink($targetPath);
            throw new FileDownloadException(sprintf('Could not open temporary file "%s"', $targetPath));
        }

        // copy at most one byte over the cap: landing exactly at cap+1 is the
        // cheapest way to tell "too big" from "exactly max size"
        $copiedBytes = stream_copy_to_stream($source, $target, static::MAX_FILE_SIZE_BYTES + 1);
        fclose($source);
        fclose($target);

        if (false === $copiedBytes || 0 === $copiedBytes) {
            @unlink($targetPath);
            throw new FileDownloadException(sprintf('Downloaded file from "%s" is empty', $url));
        }
        if ($copiedBytes > static::MAX_FILE_SIZE_BYTES) {
            @unlink($targetPath);
            throw new FileDownloadException(sprintf('File from "%s" exceeds the maximum import file size (%d bytes)', $url, static::MAX_FILE_SIZE_BYTES));
        }

        return $targetPath;
    }

    protected function copyLocalFile(string $path): string
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new FileDownloadException(sprintf('Local file "%s" does not exist or is not readable', $path));
        }

        $this->assertAllowedLocalPath($path);

        if (filesize($path) > static::MAX_FILE_SIZE_BYTES) {
            throw new FileDownloadException(sprintf('Local file "%s" exceeds the maximum import file size (%d bytes)', $path, static::MAX_FILE_SIZE_BYTES));
        }

        $targetPath = $this->createTemporaryFile();
        if (!copy($path, $targetPath)) {
            @unlink($targetPath);
            throw new FileDownloadException(sprintf('Could not copy local file "%s"', $path));
        }

        return $targetPath;
    }

    /**
     * Local paths are confined to the shop directory and the system temp dir.
     * Import is admin-only, but the fetched file becomes DOWNLOADABLE content
     * (a virtual product file): without this check a file_url cell pointing at
     * e.g. app/config/parameters.php would expose it to customers. realpath()
     * also resolves symlinks and ../ traversal before the comparison.
     */
    protected function assertAllowedLocalPath(string $path): void
    {
        $realPath = realpath($path);
        if (false === $realPath) {
            throw new FileDownloadException(sprintf('Local file "%s" does not exist', $path));
        }

        $allowedRoots = [sys_get_temp_dir()];
        if (defined('_PS_ROOT_DIR_')) {
            $allowedRoots[] = _PS_ROOT_DIR_;
        }

        foreach ($allowedRoots as $allowedRoot) {
            $realRoot = realpath($allowedRoot);
            if (false !== $realRoot && str_starts_with($realPath, rtrim($realRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)) {
                return;
            }
        }

        throw new FileDownloadException(sprintf('Local file "%s" is outside the allowed import locations', $path));
    }

    protected function createTemporaryFile(): string
    {
        // always the system temp dir: these files live only for the duration
        // of one command dispatch and are deleted by the caller
        $temporaryDirectory = sys_get_temp_dir();

        $targetPath = tempnam($temporaryDirectory, 'ps_import');
        if (false === $targetPath) {
            throw new FileDownloadException(sprintf('Could not create a temporary file in "%s"', $temporaryDirectory));
        }

        return $targetPath;
    }

    /**
     * Raw-urlencodes the path segments of the URL so spaces and non-ASCII
     * characters survive the request (legacy ImageCopier parity, without the
     * http_build_url dependency).
     */
    protected function sanitizeUrl(string $url): string
    {
        $parts = parse_url($url);
        if (false === $parts || !isset($parts['scheme'], $parts['host'])) {
            throw new FileDownloadException(sprintf('Malformed URL "%s"', $url));
        }

        $sanitized = $parts['scheme'] . '://';
        if (isset($parts['user'])) {
            $sanitized .= $parts['user'] . (isset($parts['pass']) ? ':' . $parts['pass'] : '') . '@';
        }
        $sanitized .= $parts['host'];
        if (isset($parts['port'])) {
            $sanitized .= ':' . $parts['port'];
        }

        if (isset($parts['path'])) {
            $segments = explode('/', $parts['path']);
            $segments = array_map(static fn (string $segment): string => rawurlencode(rawurldecode($segment)), $segments);
            $sanitized .= implode('/', $segments);
        }

        if (isset($parts['query'])) {
            $sanitized .= '?' . str_replace(' ', '%20', $parts['query']);
        }

        return $sanitized;
    }
}
