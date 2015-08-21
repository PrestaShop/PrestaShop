<?php
namespace PrestaShop\PrestaShop\Tests\TestCase;

use Cache;
use Configuration;
use Context;
use Db;
use PHPUnit_Framework_TestCase;
use Core_Business_ContainerBuilder;
use Core_Foundation_IoC_Container;
use Adapter_ServiceLocator;
use PrestaShop\PrestaShop\Tests\Fake\FakeConfiguration;
use PrestaShop\PrestaShop\Tests\Helper\Mocks\FakeEntityMapper;
use Phake;

class UnitTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Core_Foundation_IoC_Container
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

    public function setupDatabaseMock()
    {
        $this->database = Phake::mock('Db');
        Db::setInstanceForTesting($this->database);
    }

    public function setup()
    {
        $this->container = new Core_Foundation_IoC_Container;
        Adapter_ServiceLocator::setServiceContainerInstance($this->container);

        $this->setupDatabaseMock();

        $this->entity_mapper = new FakeEntityMapper();

        $this->container->bind('Adapter_EntityMapper', $this->entity_mapper);

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
            'Core_Business_ConfigurationInterface',
            $fakeConfiguration
        );
        return $fakeConfiguration;
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

        $container_builder = new Core_Business_ContainerBuilder;
        $container = $container_builder->build();
        Adapter_ServiceLocator::setServiceContainerInstance($container);
    }
}
