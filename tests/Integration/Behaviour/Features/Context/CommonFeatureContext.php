<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use AppKernel;
use Context;
use Employee;
use LegacyTests\PrestaShopBundle\Utils\DatabaseCreator;
use Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommonFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * PrestaShop Symfony AppKernel
     *
     * Required to access services through the container
     *
     * @var AppKernel
     */
    protected static $kernel;

    /**
     * @BeforeSuite
     */
    public static function prepare($scope)
    {
        require_once __DIR__ . '/../../bootstrap.php';

        self::$kernel = new AppKernel('test', true);
        self::$kernel->boot();

        global $kernel;
        $kernel = self::$kernel;

        $employee = new Employee();
        Context::getContext()->employee = $employee->getByEmail('test@prestashop.com');
    }

    /**
     * This hook can be used to flag a feature for database hard reset
     *
     * @BeforeFeature @reset-database-before-feature
     */
    public static function cleanDatabaseHardPrepareFeature()
    {
        DatabaseCreator::restoreTestDB();
        require_once _PS_ROOT_DIR_ . '/config/config.inc.php';
    }

    /**
     * This hook can be used to flag a scenario for database hard reset
     *
     * @BeforeScenario @database-scenario
     */
    public function cleanDatabaseHardPrepare()
    {
        DatabaseCreator::restoreTestDB();
        require_once _PS_ROOT_DIR_ . '/config/config.inc.php';
    }

    /**
     * @BeforeStep
     *
     * Clear Doctrine entity manager at each step in order to get fresh data
     */
    public function clearEntityManager()
    {
        $this::getContainer()->get('doctrine.orm.entity_manager')->clear();
    }

    /**
     * Return PrestaShop Symfony services container
     *
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        return static::$kernel->getContainer();
    }

    /**
     * @Given single shop context is loaded
     */
    public function singleShopContextIsLoaded()
    {
        Shop::setContext(Shop::CONTEXT_SHOP);
    }

    /**
     * @Given multiple shop context is loaded
     */
    public function multipleShopContextIsLoaded()
    {
        Shop::setContext(Shop::CONTEXT_ALL);
    }
}
