<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

abstract class AbstractAssetManagerCore
{
    use PrestaShop\PrestaShop\Adapter\Assets\AssetUrlGeneratorTrait;
    protected $directories;
    protected $configuration;
    protected $list = [];

    public const DEFAULT_MEDIA = 'all';
    public const DEFAULT_PRIORITY = 50;
    public const DEFAULT_JS_POSITION = 'bottom';

    public function __construct(array $directories, ConfigurationInterface $configuration)
    {
        $this->directories = $directories;
        $this->configuration = $configuration;

        $this->list = $this->getDefaultList();
    }

    abstract protected function getDefaultList();

    abstract protected function getList();

    /**
     * @param string $relativePath
     *
     * @return bool|string
     */
    public function getFullPath(string $relativePath)
    {
        foreach ($this->getDirectories() as $baseDir) {
            $fullPath = $baseDir . ltrim($relativePath, '/'); // not DIRECTORY_SEPARATOR because, it's path included manually
            if (file_exists($this->getPathFromUri($fullPath))) {
                return $fullPath;
            }
        }

        return false;
    }

    private function getDirectories()
    {
        static $directories;

        if (null === $directories) {
            foreach ($this->directories as $baseDir) {
                if (!empty($baseDir)) {
                    $directories[] = $baseDir;
                }
            }
        }

        return $directories;
    }
}
