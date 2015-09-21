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

namespace PrestaShop\PrestaShop\Core\Foundation\View;

/**
 * ViewFactory
 *
 * This factory class is responsible to load a valid rendering template engine (smarty/twig)
 */
class ViewFactory
{
    public $view;

    /**
     * Constructor
     *
     * @param \Core_Foundation_IoC_Container $container
     * @param string $engine_name
     *
     * @throws \Exception If the class defined by the engine name do not exists
     */
    public function __construct(\Core_Foundation_IoC_Container $container, $engine_name = 'smarty')
    {
        $class_view = '\\PrestaShop\\PrestaShop\\Core\\Foundation\\View\\Views\\'. ucfirst($engine_name);
        if (!class_exists($class_view)) {
            throw new \Exception('Please define a valid template engine');
        }

        $view = new $class_view($container);
        $view->parserDirectory = _PS_VENDOR_DIR_ . $engine_name;
        $view->parserCompileDirectory = _PS_CACHE_DIR_.$engine_name.'/compile';
        $view->parserCacheDirectory = _PS_CACHE_DIR_.$engine_name.'/cache';

        $this->view = $view;
    }
}
