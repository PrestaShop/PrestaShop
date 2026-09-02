<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\File;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * This class adds a cache layer on top of the standard Yaml parser for improved performance
 */
final class YamlParser
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $useCache;

    /**
     * YamlParser constructor.
     *
     * @param string $cacheDir
     * @param bool $useCache
     */
    public function __construct($cacheDir, $useCache = true)
    {
        $this->cacheDir = $cacheDir;
        $this->useCache = $useCache;
    }

    /**
     * Parse a YAML File and return the result
     *
     * @param string $sourceFile
     * @param bool $forceRefresh
     *
     * @return mixed The YAML converted to a PHP value
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParseException
     */
    public function parse($sourceFile, $forceRefresh = false)
    {
        if (!$this->useCache) {
            return Yaml::parseFile($sourceFile);
        }

        $phpConfigFile = $this->getCacheFile($sourceFile);
        // we set the debug flag to true to force the cache freshness check
        $configCache = new ConfigCache($phpConfigFile, true);
        if (!$forceRefresh && $configCache->isFresh()) {
            return require $phpConfigFile;
        }

        $config = Yaml::parseFile($sourceFile);
        $resources = [
            new FileResource($sourceFile),
        ];
        $configCache->write('<?php return ' . var_export($config, true) . ';' . PHP_EOL, $resources);

        return $config;
    }

    /**
     * @param string $sourceFile
     *
     * @return string
     */
    public function getCacheFile($sourceFile)
    {
        return sprintf(
            '%syaml/%s.php',
            $this->cacheDir,
            md5($sourceFile)
        );
    }
}
