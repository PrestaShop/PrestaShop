<?php
namespace PrestaShop\PrestaShop\tests\TestCase;

use Cache;
use Configuration;
use Context;
use Db;
use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Core\ContainerBuilder;
use \PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use Phake;
use Symfony\Component\HttpKernel\Kernel;
use PrestaShop\PrestaShop\Tests\TestCase\FakeEntityMapper;
use PrestaShop\PrestaShop\Tests\TestCase\FakeConfiguration;

class UnitTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var FakeEntityMapper
     */
    public $entity_mapper;

    /**
     * @var Context
     */
    public $context;

    /**
     * @var Db
     */
    public $database;

    /**
     * @var Cache
     */
    public $cache;

    /**
     * @var Kernel
     */
    public $sfKernel;

    public function setupDatabaseMock()
    {
        $this->database = Phake::mock('Db');
        Db::setInstanceForTesting($this->database);
    }

    public function setup()
    {
        $this->container = new Container();
        ServiceLocator::setServiceContainerInstance($this->container);

        $this->setupDatabaseMock();

        $this->entity_mapper = new FakeEntityMapper();

        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\EntityMapper', $this->entity_mapper);

        $this->context = Phake::mock('Context');

        Phake::when($this->context)->cloneContext()->thenReturn($this->context);

        $this->context->shop = Phake::mock('Shop');
        Context::setInstanceForTesting($this->context);

        $this->cache = Phake::mock('Cache');
        Cache::setInstanceForTesting($this->cache);
    }

    public function setConfiguration(array $keys)
    {
        $fakeConfiguration = new FakeConfiguration($keys);
        $this->container->bind(
            '\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface',
            $fakeConfiguration
        );
        return $fakeConfiguration;
    }

    public function setupSfKernel()
    {
        // Prepare Symfony kernel to resolve route.
        $loader = require_once __DIR__.'/../../app/bootstrap.php.cache';
        require_once __DIR__.'/../../app/AppKernel.php';
        $this->sfKernel = new \AppKernel('test', true);
        $this->sfKernel->loadClassCache();
        $this->sfKernel->boot();
        return $this->sfKernel;
    }

    public function teardown()
    {
        Cache::deleteTestingInstance();
        Db::deleteTestingInstance();
        Context::deleteTestingInstance();
        /**
         * @todo proxy static calls inside Configuration to a mockable instance
         * so that Configuration can be (indirectly) mocked.
         * This way we'll avoid doing obscure teardown stuff like below.
         */
        Configuration::clearConfigurationCacheForTesting();

        $container_builder = new ContainerBuilder();
        $container = $container_builder->build();
        ServiceLocator::setServiceContainerInstance($container);
    }
}
