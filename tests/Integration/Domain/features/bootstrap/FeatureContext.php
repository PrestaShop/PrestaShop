<?php

use Behat\Behat\Context\Context;

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

    public function __construct()
    {
    }

    /**
     * @BeforeSuite
     */
    public static function prepare($scope)
    {
        $rootDirectory = __DIR__ . '/../../../../../';

        require_once($rootDirectory . 'config/config.inc.php');
        require_once($rootDirectory . 'app/AppKernel.php');

        self::$kernel = new AppKernel('dev', true);
        self::$kernel->boot();
    }

    /**
     * @BeforeScenario
     */
    public function before(\Behat\Behat\Hook\Scope\BeforeScenarioScope $scope)
    {
        // @todo: explore ways to do this in a smart way
        //\LegacyTests\PrestaShopBundle\Utils\DatabaseCreator::restoreTestDB();
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static function getContainer()
    {
        return self::$kernel->getContainer();
    }

}
