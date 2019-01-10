<?php

use Behat\Behat\Context\Context;
use LegacyTests\PrestaShopBundle\Utils\DatabaseCreator;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    use SqlManagerContextTrait;

    /**
     * PrestaShop Symfony AppKernel
     *
     * Required to access services through the container
     *
     * @var AppKernel
     */
    protected static $kernel;

    /**
     * "When" steps perform actions, and store the latest result
     * in this variable so that "Then" action can check its content
     *
     * @var mixed
     */
    protected $latestResult;

    /** @var bool */
    protected $flag_performDatabaseCleanHard = false;
    /** @var bool */
    protected $flag_performDatabaseCleanLight = false;

    public function __construct()
    {
    }

    /**
     * @BeforeSuite
     */
    public static function prepare($scope)
    {
        $rootDirectory = __DIR__ . '/../../../../../';

        require_once $rootDirectory . 'config/config.inc.php';
        require_once $rootDirectory . 'app/AppKernel.php';

        self::$kernel = new AppKernel('dev', true);
        self::$kernel->boot();
    }

    /**
     * @BeforeScenario @database-hard-reset
     */
    public function beforeScenario_cleanDatabaseHard()
    {
        $this->flag_performDatabaseCleanHard = true;
    }

    /**
     * @BeforeScenario @database-light-reset
     */
    public function beforeScenario_cleanDatabaseLight()
    {
        $legacyDatabaseSingleton = \Db::getInstance(_PS_USE_SQL_SLAVE_);
        $legacyDatabaseSingleton->execute('START TRANSACTION;');

        $this->flag_performDatabaseCleanLight = true;
    }

    /**
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
     * Clear entity manager at each step in order to get fresh data
     */
    public function clearEntityManager()
    {
        $this::getContainer()->get('doctrine.orm.entity_manager')->clear();
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static function getContainer()
    {
        return self::$kernel->getContainer();
    }
}
