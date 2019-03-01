<?php

namespace Tests\Integration\Behaviour\Features\Context;

use AppKernel;
use Behat\Behat\Context\Context as BehatContext;
use Db;
use LegacyTests\PrestaShopBundle\Utils\DatabaseCreator;

/**
 * PrestaShopFeatureContext provides behat hooks to perform necessary operations for testing:
 * - shop setup
 * - database reset between scenario
 * - cache clear between steps
 * - ...
 */
abstract class AbstractPrestaShopFeatureContext implements BehatContext
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
     * "When" steps perform actions, and some of them store the latest result
     * in this variable so that "Then" action can check its content
     *
     * @var mixed
     */
    protected $latestResult;

    /** @var bool */
    protected $flagPerformDatabaseCleanHard = false;

    /**
     * @BeforeSuite
     */
    public static function prepare($scope)
    {
        require_once __DIR__ . '/../../bootstrap.php';

        self::$kernel = new AppKernel('test', true);
        self::$kernel->boot();
    }

    /**
     * This hook can be used to flag a feature for database hard reset
     *
     * @BeforeFeature @database-feature
     */
    static public function cleanDatabaseHardPrepareFeature()
    {
        DatabaseCreator::restoreTestDB();
    }

    /**
     * This hook can be used to flag a scenario for database hard reset
     *
     * @BeforeScenario @database-scenario
     */
    public function cleanDatabaseHardPrepare()
    {
        DatabaseCreator::restoreTestDB();
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
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static function getContainer()
    {
        return static::$kernel->getContainer();
    }
}
