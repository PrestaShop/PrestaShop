<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module;

use PrestaShop\PrestaShop\Core\Util\ArrayFinder;

class ConfigReader implements ConfigReaderInterface
{
    /**
     * @var string
     */
    protected $modulesDirectoryPath;

    public function __construct(string $modulesDirectoryPath)
    {
        $this->modulesDirectoryPath = $modulesDirectoryPath;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $name, string $isoCode): ?ArrayFinder
    {
        $configFile = $this->findConfigFile($name, $isoCode);
        if ($configFile === null) {
            return null;
        }

        libxml_use_internal_errors(true);

        $xml = @simplexml_load_file($configFile);
        $errors = libxml_get_errors();

        if ($xml === false || !empty($errors)) {
            return null;
        }

        $result = [];

        foreach ($xml as $node) {
            $result[$node->getName()] = (string) $node;
        }

        return new ArrayFinder($result);
    }

    /**
     * Find config file depending on the iso code.
     *
     * @param string $name The module name
     * @param string $isoCode The current iso code format fr_FR
     *
     * @return string|null
     */
    protected function findConfigFile(string $name, string $isoCode): ?string
    {
        $iso = substr($isoCode, 0, 2);

        $configFile = $this->modulesDirectoryPath . $name . '/config_' . $iso . '.xml';

        // For "en" iso code, we keep the default config.xml name
        if ($iso === 'en' || !file_exists($configFile)) {
            $configFile = $this->modulesDirectoryPath . $name . '/config.xml';

            if (!file_exists($configFile)) {
                return null;
            }
        }

        return $configFile;
    }
}
