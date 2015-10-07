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
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * This context contains application global information as main parameters.
 *
 * Kind of data you will find in: language, user/employee, session data, etc...
 *
 * To retrieve the Context, do not instantiate by yourself. Call it from the container:
 * $container->make('CoreBusiness:Context');, or with alias $container->make('Context');
 */
class Context extends ParameterBag
{
    private static $instantiated = false;

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
     *
     * @param \Adapter_LegacyContext $legacyContext Given by IoC
     * @param Container $container The application container
     */
    final public function __construct(\Adapter_LegacyContext $legacyContext, Container $container)
    {
        if (self::$instantiated == true) {
            throw new DevelopmentErrorException('The Context cannot be instantiated twice. Please call it from container.', get_class($this), 2011);
        }
        self::$instantiated = true;

        $configuration = $container->make('Core_Business_ConfigurationInterface');

        // Default values now.
        $this->set('app_entry_point', 'unknown'); // admin / front / unknown
        $this->set('debug', $configuration->get('_PS_MODE_DEV_'));

        // While Legacy architecture is here, retrieve some data from it.
        $legacyContext->mergeContextWithLegacy($this);

        // Override legacy values & define new Architecture values.
        //here
    }
}
