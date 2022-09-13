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

use PrestaShopBundle\Install\Install;
use Symfony\Component\Yaml\Yaml;
use PrestaShop\PrestaShop\Core\Util\ArrayFinder;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;

/**
 * Step 5: configure content
 */
class InstallControllerHttpContent extends InstallControllerHttp implements HttpConfigureInterface
{
    public const MODULES_ALL = 0;
    public const MODULES_SELECTED = 1;

    /**
     * Modules present on the disk
     *
     * @var array
     */
    public $modules = [];

    /**
     * Themes present on the disk
     *
     * @var array
     */
    public $themes = [];

    /**
     * Define the current action for modules
     *
     * @var int
     */
    public $moduleAction = self::MODULES_ALL;

    /**
     * Define if the select all modules is selected
     *
     * @var bool
     */
    public $selectAllButton = false;

    public function init(): void
    {
        $this->model = new Install();
        $this->modules = $this->model->getModulesOnDisk();
        $this->themes = $this->model->getThemesOnDisk();
        if ($this->session->content_install_fixtures === null) {
            $this->session->content_install_fixtures = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processNextStep(): void
    {
        $moduleAction = (int) Tools::getValue('module-action');
        if (!in_array($moduleAction, [static::MODULES_ALL, static::MODULES_SELECTED])) {
            $moduleAction = static::MODULES_ALL;
        }

        if ($moduleAction !== static::MODULES_ALL) {
            $this->session->content_modules = Tools::getValue('modules', []);
        } else {
            $this->session->content_modules = [];
            foreach ($this->modules as $module) {
                $this->session->content_modules[] = $module->get('name');
            }
        }

        $this->session->moduleAction = $moduleAction;
        $this->session->content_theme = Tools::getValue('theme', null);
        if (Tools::getIsset('install-fixtures')) {
            $this->session->content_install_fixtures = (bool) Tools::getValue('install-fixtures', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(): bool
    {
        return !empty($this->session->content_theme);
    }

    /**
     * {@inheritdoc}
     */
    public function display(): void
    {
        if ($this->session->content_theme === null) {
            foreach ($this->themes as $theme) {
                if ($theme->get('name') === Install::DEFAULT_THEME) {
                    $this->session->content_theme = $theme->get('name');
                    break;
                }
            }
        }

        $this->moduleAction = $this->session->moduleAction ?? static::MODULES_ALL;
        $this->selectAllButton = $this->session->content_modules === null || count($this->modules) === count($this->session->content_modules);

        $this->displayContent('content');
    }

    public function getModulesPerCategories(): array
    {
        $yamlParser = new YamlParser(_PS_CACHE_DIR_);
        $prestashopAddonsConfig = $yamlParser->parse(_PS_ROOT_DIR_ . '/app/config/addons/categories.yml');
        $categoriesProvider = new CategoriesProvider(
            $prestashopAddonsConfig['prestashop']['addons']['categories'],
            []
        );

        $categories = $categoriesProvider->getCategories()['categories']->subMenu;
        foreach ($this->modules as $module) {
            $tab = $this->findModuleCategory($module, $categories);
            $categories[$tab]->modules[] = $module;
        }

        foreach ($categories as $category) {
            uasort($category->modules, [$this, 'sortModulesByDisplayname']);
        }

        return $categories;
    }

    protected function sortModulesByDisplayName(ArrayFinder $a, ArrayFinder $b): int
    {
        return $a->get('displayName') <=> $b->get('displayName');
    }

    protected function findModuleCategory(ArrayFinder $module, array $categories)
    {
        $tab = $module->get('tab');
        if (!empty($tab)) {
            foreach ($categories as $category) {
                if ($tab === $category->tab) {
                    return $category->name;
                }
            }
        }

        return CategoriesProvider::CATEGORY_OTHER;
    }
}
