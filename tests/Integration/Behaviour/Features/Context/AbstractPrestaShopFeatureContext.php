<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
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
     * @var \AppKernel
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
    protected $flag_performDatabaseCleanHard = false;
    /** @var bool */
    protected $flag_performDatabaseCleanLight = false;

    /**
     * @BeforeSuite
     */
    public static function prepare($scope)
    {
        require_once __DIR__ . '/bootstrap.php';

        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();
    }

    /**
     * This hook can be used to flag a scenario as eligible for database hard reset
     *
     * @BeforeScenario @database-hard-reset
     */
    public function beforeScenario_cleanDatabaseHard()
    {
        $this->flag_performDatabaseCleanHard = true;
    }

    /**
     * This hook can be used to flag a scenario as eligible for database light reset
     *
     * @BeforeScenario @database-light-reset
     */
    public function beforeScenario_cleanDatabaseLight()
    {
        $legacyDatabaseSingleton = \Db::getInstance(_PS_USE_SQL_SLAVE_);
        $legacyDatabaseSingleton->execute('START TRANSACTION;');

        $this->flag_performDatabaseCleanLight = true;
    }

    /**
     * This hook can be used to perform a database hard reset if the scenario has been flagged accordingly
     *
     * @AfterScenario @database-hard-reset
     */
    public function afterScenario_cleanDatabaseHard()
    {
        if (true === $this->flag_performDatabaseCleanHard) {
            $this->flag_performDatabaseCleanHard = false;
            DatabaseCreator::restoreTestDB();
        }
    }

    /**
     * This hook can be used to perform a database light reset if the scenario has been flagged accordingly
     *
     * @AfterScenario @database-light-reset
     */
    public function afterScenario_cleanDatabaseLight()
    {
        if (true === $this->flag_performDatabaseCleanLight) {
            $legacyDatabaseSingleton = \Db::getInstance(_PS_USE_SQL_SLAVE_);
            $legacyDatabaseSingleton->execute('ROLLBACK');

            $this->flag_performDatabaseCleanLight = false;
        }
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
