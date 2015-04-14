<?php
namespace PrestaShop\PrestaShop\Tests\TestCase;

use Exception;

use Cache;
use Context;
use Configuration;
use Db;
use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Tests\Helper\Mocks\DbMock;
use PrestaShop\PrestaShop\Tests\Helper\Mocks\CacheMock;

use Phake;

class UnitTestCase extends PHPUnit_Framework_TestCase
{
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

	public function setUpCommonStaticMocks()
	{
		$this->setupDatabaseMock();

		$this->context = Phake::mock('Context');

		Phake::when($this->context)->cloneContext()->thenReturn($this->context);

		$this->context->shop = Phake::mock('Shop');
		Context::setInstanceForTesting($this->context);

		$this->cache = Phake::mock('Cache');
		Cache::setInstanceForTesting($this->cache);
	}

	public function tearDownCommonStaticMocks()
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
	}

}
