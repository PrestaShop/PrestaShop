<?php

namespace PrestaShop\PrestaShop\Tests\Helper\Mocks;

use Cache;

/**
 * Class CacheMock
 * @package PrestaShop\PrestaShop\Tests\Helper\Mocks
 *
 * This mock class is defined only because of PHPUnit limitation :
 * We can't instantiate a full mock for an abstract class with getMockForAbstractClass.
 */
class CacheMock extends Cache
{
	protected function _set($key, $value, $ttl = 0){}
	protected function _get($key){}
	protected function _exists($key){}
	protected function _delete($key){}
	protected function _writeKeys(){}
	public function flush(){}
}
