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

namespace PrestaShop\PrestaShop\Core\Theme;

use PrestaShop\PrestaShop\Core\Util\ArrayFinder;
use Symfony\Component\Yaml\Parser;

class ConfigReader implements ConfigReaderInterface
{
    public const DEFAULT_CONFIGURATION_THEME = [
        'display_name' => 'N/A',
        'version' => 'N/A',
        'preview' => 'themes/preview-fallback.png',
    ];

    /**
     * @var string
     */
    protected $themesDirectoryPath;

    public function __construct(string $themesDirectoryPath)
    {
        $this->themesDirectoryPath = $themesDirectoryPath;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $name): ?ArrayFinder
    {
        $configFile = $this->findConfigFile($name);
        if ($configFile === null) {
            return null;
        }

        $themeData = (new Parser())->parse(file_get_contents($configFile));

        if (file_exists($this->themesDirectoryPath . $name . '/preview.png')) {
            $themeData['preview'] = 'themes/' . $name . '/preview.png';
        }
        $themeData = array_merge(self::DEFAULT_CONFIGURATION_THEME, $themeData);

        return new ArrayFinder($themeData);
    }

    /**
     * Find config file depending on the iso code.
     *
     * @param string $name The module name
     *
     * @return string|null
     */
    protected function findConfigFile(string $name): ?string
    {
        $configFile = $this->themesDirectoryPath . $name . '/config/theme.yml';

        if (!file_exists($configFile)) {
            return null;
        }

        return $configFile;
    }
}
