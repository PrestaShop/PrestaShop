<?php

namespace PrestaShop\Test\Stubs;

class Db extends \DbCore {
	
	public function __construct() {}
	
	public function connect() {}
	public function disconnect() {}
	protected function _query($sql) {}
	protected function _numRows($result) {}
	public function Insert_ID() {}
	public function Affected_Rows() {}
	public function nextRow($result = false) {}
	public function getVersion() {}
	public function _escape($str) {}
	public function getMsgError() {}
	public function getNumberError() {}
	public function set_db($db_name) {}
	
}