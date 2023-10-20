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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module\SourceHandler;

use PrestaShop\PrestaShop\Core\Module\SourceHandler\Exception\SourceNotHandledException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RemoteZipSourceHandler implements SourceHandlerInterface
{
    private const ZIP_FILENAME_PATTERN = '/(\w+)\.zip\b/';

    /**
     * @var ZipSourceHandler
     */
    private $zipSourceHandler;

    /**
     * @var string
     */
    private $downloadDir;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var string|null
     */
    private $moduleName;

    /**
     * @var mixed
     */
    private $handledSource;

    public function __construct(
        ZipSourceHandler $zipSourceHandler,
        HttpClientInterface $httpClient,
        string $downloadDir
    ) {
        $this->zipSourceHandler = $zipSourceHandler;
        $this->httpClient = $httpClient;
        $this->downloadDir = $downloadDir;
    }

    public function canHandle($source): bool
    {
        if (!is_string($source)) {
            return false;
        }

        try {
            $response = $this->httpClient->request('HEAD', $source);
        } catch (TransportExceptionInterface $e) {
            return false;
        }

        $this->moduleName = null;

        if (preg_match(self::ZIP_FILENAME_PATTERN, $source, $moduleName) === 1) {
            $this->moduleName = $moduleName[1];
        }

        $headers = $response->getHeaders(false);

        if (isset($headers['content-disposition'])
            && preg_match(self::ZIP_FILENAME_PATTERN, reset($headers['content-disposition']), $moduleName) === 1
        ) {
            $this->moduleName = $moduleName[1];
        }

        if (!empty($this->moduleName)
            && $response->getStatusCode() === 200
            && isset($headers['content-type'])
            && reset($headers['content-type']) === 'application/zip'
        ) {
            $this->handledSource = $source;

            return true;
        }

        return false;
    }

    public function getModuleName($source): ?string
    {
        $this->assertSourceHasBeenChecked($source);

        return $this->moduleName;
    }

    public function handle(string $source): void
    {
        $this->assertSourceHasBeenChecked($source);

        $filesystem = new Filesystem();
        $path = $this->getDownloadDir($this->getModuleName($source));
        $filesystem->mkdir(dirname($path));
        $filesystem->dumpFile($path, $this->httpClient->request('GET', $source)->getContent());
        $this->zipSourceHandler->handle($path);
    }

    private function getDownloadDir(string $moduleName): string
    {
        return implode(DIRECTORY_SEPARATOR, [$this->downloadDir, $moduleName . '.zip']);
    }

    private function assertSourceHasBeenChecked($source): void
    {
        if ($source !== $this->handledSource) {
            throw new SourceNotHandledException('Method canHandle() should be called first');
        }
    }
}
