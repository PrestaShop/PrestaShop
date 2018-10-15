<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Service\DataProvider\Admin;

use Symfony\Component\Routing\Router;
use Module;

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
     * Gets all recommended modules for a specific domain.
     *
     * @param string $domain The given domain to filter recommended modules
     * @param bool|false $randomize To shuffle results
     *
     * @return array A list of modules names (identifiers)
     */
    public function getRecommendedModuleIdList($domain = 'administration', $randomize = false)
    {
        // FIXME: replace static by dynamic call from add-ons when available
        switch ($domain) {
            case 'products_quantity':
                return ['pm_advancedpack', 'quotation', 'amazon', 'pushoncart', 'cartabandonmentpro'];
            case 'products_shipping':
                return ['orderpreparation', 'pqeasypost', 'printlabels', 'upstrackingv2'];
            case 'products_price':
                return ['ordertaxprofitreport', 'massiveprices', 'giftcards', 'groupinc', 'moneybookers', 'authorizeaim'];
            case 'products_seo':
                return ['ganalytics', 'gshopping', 'leguide', 'seoexpert', 'pm_seointernallinking', 'ec_seo404'];
            case 'products_options':
                return ['pm_multiplefeatures', 'pm_advancedsearch4', 'banipmod', 'mynewsletter', 'allinone_rewards', 'pm_cachemanager', 'lgcookieslaw', 'customfields'];
            case 'products_others':
            default:
                return [];
        }
    }

    /**
     * Filters the given module list to remove installed ones, and bad filled cases.
     *
     * @param array $moduleFullList The input list to filter
     *
     * @return array The filtered list of modules
     */
    public function filterInstalledAndBadModules(array $moduleFullList)
    {
        $installed_modules = [];
        array_map(function ($module) use (&$installed_modules) {
            $installed_modules[$module['name']] = $module;
        }, Module::getModulesInstalled());

        foreach ($moduleFullList as $key => $module) {
            if ((bool) array_key_exists($module->attributes->get('name'), $installed_modules) === true) {
                unset($moduleFullList[$key]);
            }
            if (!isset($module->attributes->get('media')->img)) {
                unset($moduleFullList[$key]);
            }
        }

        return $moduleFullList;
    }
}
