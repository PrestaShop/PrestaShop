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
