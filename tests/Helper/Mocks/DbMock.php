<?php

namespace PrestaShop\PrestaShop\Tests\Helper\Mocks;

use Db;

/**
 * Class DbMock
 * @package PrestaShop\PrestaShop\Tests\Helper\Mocks
 *
 * This mock class is defined only because of PHPUnit limitation :
 * We can't instantiate a full mock for an abstract class with getMockForAbstractClass.
 */
class DbMock extends Db
{
	public function __construct(){}
	public function connect(){}
	public function disconnect(){}
	protected function _query($sql){}
	protected function _numRows($result){}
	public function Insert_ID(){}
	public function Affected_Rows(){}
	public function nextRow($result = false){}
	protected function getAll($result = false){}
	public function getVersion(){}
	public function _escape($str){}
	public function getMsgError(){}
	public function getNumberError(){}
	public function set_db($db_name){}
	public function getBestEngine(){}
}
