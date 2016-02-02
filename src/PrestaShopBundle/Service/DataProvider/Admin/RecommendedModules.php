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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Service\DataProvider\Admin;

use Symfony\Component\Routing\Router;

/**
 * Data provider for new Architecture, about recommended modules.
 *
 * This class will provide modules data from Add-ons API depending on the domain to display recommended modules.
 */
class RecommendedModules
{

    /**
     * @var Router
     */
    private $router;

    /**
     * Constructor.
     *
     * Dependency injection will give the required services.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Gets all recommended modules for a specific domain
     *
     * @param string $domain The given domain to filter recommended modules
     * @param bool|false $randomize To shuffle results
     * @return array A list of modules names (identifiers)
     */
    public function getRecommendedModuleIdList($domain = 'administration', $randomize = false)
    {
        return ['twengafeed', 'gsitemap', 'feeder', 'gtrustedstores']; // FIXME
    }

    /**
     * Filters the given module list to remove installed ones, and bad filled cases.
     *
     * @param array $moduleFullList The input list to filter
     * @return array The filtered list of modules
     */
    public function filterInstalledAndBadModules(array $moduleFullList)
    {
        $installed_modules = [];
        array_map(function ($module) use (&$installed_modules) {
            $installed_modules[$module['name']] = $module;
        }, \Module::getModulesInstalled());

        foreach ($moduleFullList as $key => $module) {
            if ((bool)array_key_exists($module->name, $installed_modules) === true) {
                unset($moduleFullList[$key]);
            }
            if (!isset($module->media->img)) {
                unset($moduleFullList[$key]);
            }
        }

        return $moduleFullList;
    }

    /**
     * Add URLs data to the modules of the given list, to be ready to display via twig template.
     *
     * @param array $products
     * @return array The same array with modules completed.
     */
    public function generateModuleUrls(array $products)
    {
        foreach ($products as &$product) {
            $product->urls = [];
            foreach (['install', 'uninstall', 'enable', 'disable', 'reset', 'update'] as $action) {
                $product->urls[$action] = $this->router->generate('admin_module_manage_action', [
                    'action' => $action,
                    'module_name' => $product->name,
                ]);
            }
            $product->urls['configure'] = $this->router->generate('admin_module_configure_action', [
                'module_name' => $product->name,
            ]);

            // Which button should be displayed first ?
            $product->url_active = '';
            if (isset($product->installed) && $product->installed == 1) {
                if ($product->active == 0) {
                    $product->url_active = 'enable';
                } elseif ($product->is_configurable == 1) {
                    $product->url_active = 'configure';
                } else {
                    $product->url_active = 'disable';
                }
            } elseif (isset($product->origin) && in_array($product->origin, ['native', 'native_all', 'partner', 'customer'])) {
                $product->url_active = 'install';
            }
        }

        return $products;
    }
}
