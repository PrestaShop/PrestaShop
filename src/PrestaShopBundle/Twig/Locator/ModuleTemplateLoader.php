<?php

/**
 * 2007-2018 PrestaShop
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
namespace PrestaShopBundle\Twig\Locator;

use Twig\Loader\FilesystemLoader;

/**
 * Loads templates from PrestaShop modules.
 */
class ModuleTemplateLoader extends FilesystemLoader
{
    /**
     * @param array $namespaces  A collection of path namespaces with namespace names.
     * @param array $modulePaths A path or an array of paths where to look for module templates
     */
    public function __construct(array $namespaces, array $modulePaths = array())
    {
        if (!empty($modulePaths)) {
            $this->registerNamespacesFromConfig($modulePaths, $namespaces);
        }
    }

    /**
     * Register namespaces in module and link them to the right paths.
     *
     * @param array $modulePaths
     * @param array $namespaces
     */
    private function registerNamespacesFromConfig(array $modulePaths, array $namespaces)
    {
        foreach ($namespaces as $namespace => $namespacePath) {
            $templatePaths = array();

            foreach ($modulePaths as $path) {
                if (is_dir($dir = $path . '/views/PrestaShop/' . $namespacePath)) {
                    $templatePaths[] = $dir;
                }
            }
            $this->setPaths($templatePaths, $namespace);
        }
    }
}
