<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        } catch (TransportExceptionInterface) {
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

        $contentType = isset($headers['content-type']) ? reset($headers['content-type']) : null;

        if (!empty($this->moduleName)
            && $response->getStatusCode() === 200
            && (
                $contentType === 'application/zip' || $contentType === 'application/octet-stream'
            )
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
