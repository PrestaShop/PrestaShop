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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Business;

use Symfony\Component\HttpFoundation\ParameterBag;

class Context extends ParameterBag
{
    /**
     * @var Context
     */
    private static $instance = null;

    /**
     * @var \Core_Foundation_IoC_Container
     */
    private static $container = null;

    /**
     * Get current instance of filled context (singleton)
     *
     * @param \Core_Foundation_IoC_Container $container The service container
     * @return Context
     */
    final public static function getInstance(\Core_Foundation_IoC_Container &$container = null)
    {
        if (!self::$instance) {
            if ($container != null) {
                self::$container = $container;
            }
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construct Core Context. This will contains data from old legacy context, and new data structure from new Core architecture.
     *
     * Initial behavior:
     * - use default values,
     * - override with legacy context values,
     * - override with new Core context values.
     * Transitional behavior:
     * - add overrides on last step when a legacy data is transferred in the new Core
     * Final behavior:
     * - suppress 'override with legacy context' step.
     */
    final public function __construct()
    {
        // TODO : default values now.

        // While Legacy architecture is here, retrieve some data from it.
        $legacyContext = self::$container->make('Adapter_LegacyContext');
        $legacyContext->mergeContextWithLegacy($this);

        // TODO : override legacy values now.
    }
}
