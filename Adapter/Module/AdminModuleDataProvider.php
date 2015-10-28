<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author     PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Admin\AbstractAdminQueryBuilder;
use PrestaShopBundle\Service\DataProvider\Admin\ModuleInterface;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheInterface;

/**
 * Data provider for new Architecture, about Module object model.
 *
 * This class will provide data from DB / ORM about Modules for the Admin interface.
 * This is an Adapter that works with the Legacy code and persistence behaviors.
 *
 * FIXME: rewrite persistence of filter parameters -> into DB
 */
class AdminModuleDataProvider extends AbstractAdminQueryBuilder implements ModuleInterface
{
    const _CACHEFILE_CATEGORIES_ = 'catalog_categories.json';
    const _CACHEFILE_MODULES_ = 'catalog_modules.json';

    /* Cache for One Day */
    const _WATCH_DOG_ = 86400;

    private $is_employee_addons_logged = false;
    private $kernel;

    protected $catalog_categories      = [];
    protected $catalog_modules         = [];

    public function __construct(\AppKernel $kernel)
    {
        $this->kernel = $kernel;
        $context = \Context::getContext();
        if (isset($context->cookie->username_addons) && isset($context->cookie->password_addons)
            && !empty($context->cookie->username_addons) && !empty($context->cookie->password_addons)) {
            $this->is_employee_addons_logged = true;
        }
    }

    public function getAllModules()
    {
        return \Module::getModulesOnDisk(true,
                (bool)$this->is_employee_addons_logged,
                (int)\Context::getContext()->employee->id);
    }

    public function getCatalogModules()
    {
        if (count($this->catalog_modules) === 0) {
            $this->loadCatalogData();
        }

        return $this->catalog_modules;
    }

    public function getCatalogCategories()
    {
        if (count($this->catalog_categories) === 0) {
            $this->loadCatalogData();
        }

        return $this->catalog_categories;
    }

    private function getModuleCache($file)
    {
        $cacheFile = $this->kernel->getCacheDir().'/modules/'.$file;

        if (file_exists($cacheFile)) {
            if ((filemtime($cacheFile) + self::_WATCH_DOG_) <= time()) {
                return false;
            }

            $fh = fopen($cacheFile, 'r');
            $cache = trim(fgets($fh));

            if ($cache) {
                return json_decode($cache);
            }
        }

        return false;
    }

    protected function loadCatalogData()
    {
        $this->catalog_categories = $this->getModuleCache(self::_CACHEFILE_CATEGORIES_);
        $this->catalog_modules    = $this->getModuleCache(self::_CACHEFILE_MODULES_);

        if (!$this->catalog_categories || !$this->catalog_modules) {
            $addons_modules = \Tools::addonsRequest('must-have');
            $partners_modules = \Tools::addonsRequest('partner');
            $natives_modules = \Tools::addonsRequest('native');

            if (!$addons_modules || !$partners_modules || !$natives_modules) {
                return false;
            }

            $json_addons_modules = json_decode($addons_modules);
            $json_partners_modules = json_decode($partners_modules);
            $json_natives_modules = json_decode($natives_modules);

            if ($json_addons_modules !== false && $json_partners_modules !== false && $json_natives_modules !== false) {
                $jsons = array_merge($json_addons_modules->modules, $json_natives_modules->modules, $json_partners_modules->products);

                $this->catalog_categories = $this->getCategoriesFromJson($jsons);
                $this->catalog_modules    = $this->convertJsonForNewCatalog($jsons);
                $this->registerModuleCache(self::_CACHEFILE_CATEGORIES_, $this->catalog_categories);
                $this->registerModuleCache(self::_CACHEFILE_MODULES_, $this->catalog_modules);
            }
        }
    }

    protected function getCategoriesFromJson($original_json)
    {
        $categories = [];

        // First Tab: Catalog
        $categories['catalog'] = $this->createMenuObject('selection',
            'Our selection');

        // Second Tab: Categories
        $categories['categories'] = $this->createMenuObject('categories',
            'Categories');

        foreach ($original_json as $module_key => $module) {
            $name = $module->categoryName;
            $ref  = $this->getRefFromModuleCategoryName($name);

            if (!array_key_exists($ref, $categories['categories']->subMenu)) {
                $categories['categories']->subMenu[$ref] = $this->createMenuObject($ref,
                    $name);
            }

            $categories['categories']->subMenu[$ref]->modulesRef[] = $module_key;
        }

        return $categories;
    }

    protected function convertJsonForNewCatalog($original_json)
    {
        $remixed_json = [];
        foreach ($original_json as $module) {
            // Add un-implemented properties
            $module->refs       = (array)$this->getRefFromModuleCategoryName($module->categoryName);
            $module->conditions = [];
            $module->rating     = (object)[
                    'score' => 0.0,
                    'countReviews' => 0,
            ];
            $module->scoring    = 0;
            $module->media      = (object)[
                    'img' => $module->img,
                    'badges' => isset($module->badges)?$module->badges:[],
                    'cover' => isset($module->cover)?$module->cover:[],
                    'screenshotsUrls' => [],
                    'videoUrl' => null,
            ];
            unset($module->badges);
            //unset($module->categoryName);
            unset($module->cover);

            $remixed_json[] = $module;
        }

        return $remixed_json;
    }

    protected function createMenuObject($ref, $name)
    {
        return (object)[
                'name' => $name,
                'refMenu' => $ref,
                'subMenu' => [],
                'modulesRef' => [],
        ];
    }

    protected function getRefFromModuleCategoryName($name)
    {
        return \Tools::replaceAccentedChars(str_replace([' '], ['_'],
                    strtolower($name)));
    }

    private function registerModuleCache($file, $data)
    {
        $cache = (new ConfigCacheFactory(true))->cache($this->kernel->getCacheDir().'/modules/'.$file, function () {});
        $cache->write(json_encode($data));

        return $cache->getPath();
    }
}
