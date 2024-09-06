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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
