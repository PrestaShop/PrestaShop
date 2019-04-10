<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
