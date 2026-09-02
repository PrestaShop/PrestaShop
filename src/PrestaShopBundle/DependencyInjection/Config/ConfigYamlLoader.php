<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\DependencyInjection\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

/**
 * ConfigYamlLoader is used to load YAML config files and get the config as array.
 */
class ConfigYamlLoader extends FileLoader
{
    private $configValues = [];

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);
        $configValues = Yaml::parse(file_get_contents($path), Yaml::PARSE_CONSTANT);

        $this->parseImports($configValues, $path);
        unset($configValues['imports']);

        $this->configValues = array_merge_recursive($this->configValues, $configValues);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
    }

    /**
     * Returns the parsed config after the YAML file has been loaded.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->configValues;
    }

    /**
     * Parses all imports.
     *
     * @param array $content
     * @param string $file
     */
    private function parseImports(array $content, $file)
    {
        if (!isset($content['imports'])) {
            return;
        }

        if (!\is_array($content['imports'])) {
            throw new InvalidArgumentException(sprintf('The "imports" key should contain an array in %s. Check your YAML syntax.', $file));
        }

        $defaultDirectory = \dirname($file);
        foreach ($content['imports'] as $import) {
            if (!\is_array($import)) {
                $import = ['resource' => $import];
            }
            if (!isset($import['resource'])) {
                throw new InvalidArgumentException(sprintf('An import should provide a resource in %s. Check your YAML syntax.', $file));
            }

            $this->setCurrentDir($defaultDirectory);
            $this->import($import['resource'], isset($import['type']) ? $import['type'] : null, isset($import['ignore_errors']) ? (bool) $import['ignore_errors'] : false, $file);
        }
    }
}
