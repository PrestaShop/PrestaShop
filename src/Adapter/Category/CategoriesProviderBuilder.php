<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Category;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;

class CategoriesProviderBuilder
{
    public function __construct(
        private readonly LegacyContext $context,
        private readonly ThemeRepository $themeRepository,
        private readonly string $cacheDir,
        private readonly string $rootDir,
        private readonly string $categoriesConfigPath,
    ) {
    }

    public function build(): CategoriesProvider
    {
        $yamlParser = new YamlParser($this->cacheDir);
        $addonsCategories = $yamlParser->parse($this->rootDir . $this->categoriesConfigPath);
        $themeName = $this->context->getContext()->shop->theme_name;
        $modulesTheme = $themeName ? $this->themeRepository->getInstanceByName($themeName)->getModulesToEnable() : [];

        return new CategoriesProvider($addonsCategories['prestashop']['addons']['categories'], $modulesTheme);
    }
}
