<?php
namespace PrestaShop\PrestaShop\Tests\TestCase;

use Exception;

use Cache;
use Context;
use Configuration;
use Db;
use PHPUnit_Framework_TestCase;

use Core_Foundation_IoC_ContainerBuilder;
use Core_Foundation_IoC_Container;
use Adapter_ServiceLocator;

use PrestaShop\PrestaShop\Tests\Fake\FakeConfiguration;

use Phake;

class UnitTestCase extends PHPUnit_Framework_TestCase
{
	protected $container;

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

		$this->context = Phake::mock('Context');

		Phake::when($this->context)->cloneContext()->thenReturn($this->context);

		$this->context->shop = Phake::mock('Shop');
		Context::setInstanceForTesting($this->context);

		$this->cache = Phake::mock('Cache');
		Cache::setInstanceForTesting($this->cache);
	}

	public function setConfiguration(array $keys)
    {
        $this->container->bind(
            'Core_Business_Configuration',
            new FakeConfiguration($keys)
        );
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

		$container_builder = new Core_Foundation_IoC_ContainerBuilder;
        $container = $container_builder->build();
        Adapter_ServiceLocator::setServiceContainerInstance($container);
	}

}
