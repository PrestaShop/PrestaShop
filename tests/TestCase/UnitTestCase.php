<?php


namespace PrestaShop\PrestaShop\Tests\TestCase;


use Cache;
use Context;
use Configuration;
use Db;
use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Tests\Helper\Mocks\DbMock;
use PrestaShop\PrestaShop\Tests\Helper\Mocks\CacheMock;

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

	public function setUpCommonStaticMocks()
	{
		$this->database = $this->getMockBuilder('PrestaShop\PrestaShop\Tests\Helper\Mocks\DbMock')->getMock();
		Db::setInstanceForTesting($this->database);

		$this->context = $this->getMockBuilder('Context')->getMock();
		$this->context->shop = $this->getMockBuilder('Shop')->getMock();
		Context::setInstanceForTesting($this->context);

		$this->cache = $this->getMockBuilder('PrestaShop\PrestaShop\Tests\Helper\Mocks\CacheMock')->getMock();
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
