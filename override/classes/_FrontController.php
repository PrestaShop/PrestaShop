<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


function developpementErrorHandler($errno, $errstr, $errfile, $errline)
{
	if (!(error_reporting() & $errno))
		return;
	switch($errno)
	{
		case E_ERROR:
			echo '[PHP Error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_WARNING:
			echo '[PHP Warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_PARSE:
			echo '[PHP Parse #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_NOTICE:
			echo '[PHP Notice #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_CORE_ERROR:
			echo '[PHP Core #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_CORE_WARNING:
			echo '[PHP Core warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_COMPILE_ERROR:
			echo '[PHP Compile #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_COMPILE_WARNING:
			echo '[PHP Compile warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_USER_ERROR:
			echo '[PHP Error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_USER_WARNING:
			echo '[PHP User warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_USER_NOTICE:
			echo '[PHP User notice #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_STRICT:
			echo '[PHP Strict #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		case E_RECOVERABLE_ERROR:
			echo '[PHP Recoverable error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
			break;
		default:
			echo '[PHP Unknown error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')';
	}
	die;
	return true;
}


class FrontController extends FrontControllerCore
{
	public $_memory = array();
	public $_time = array();
	
	private function displayMemoryColor($n)
	{
		$n /= 1048576;
		if ($n > 3)
			return '<span style="color:red">'.round($n, 2).' Mb</span>';
		if ($n > 1)
			return '<span style="color:orange">'.round($n, 2).' Mb</span>';
		return '<span style="color:green">'.round($n, 2).' Mb</span>';
	}
	
	private function displaySQLQueries($n)
	{
		if ($n > 150)
			return '<span style="color:red">'.$n.' queries</span>';
		if ($n > 100)
			return '<span style="color:orange">'.$n.' queries</span>';
		return '<span style="color:green">'.$n.' quer'.($n == 1 ? 'y' : 'ies').'</span>';
	}
	
	private function displayLoadTimeColor($n, $kikoo = false)
	{
		if ($n > 1)
			return '<span style="color:red">'.round($n, 3).'s</span>'.($kikoo ? '<br />You\'d better run your shop on a toaster' : '');
		if ($n > 0.5)
			return '<span style="color:orange">'.round($n * 1000).'ms</span>'.($kikoo ? '<br />I hope it is a shared hosting' : '');
		return '<span style="color:green">'.round($n * 1000).'ms</span>'.($kikoo ? '<br />Good boy! That\'s what I call a webserver!' : '');
	}
	
	private function getTimeColor($n)
	{
		if ($n > 4)
			return 'style="color:red"';
		if ($n > 2)
			return 'style="color:orange"';
		return 'style="color:green"';
	}
	
	private function getQueryColor($n)
	{
		if ($n > 5)
			return 'style="color:red"';
		if ($n > 2)
			return 'style="color:orange"';
		return 'style="color:green"';
	}
	
	private function getTableColor($n)
	{
		if ($n > 30)
			return 'style="color:red"';
		if ($n > 20)
			return 'style="color:orange"';
		return 'style="color:green"';
	}
	
	public function __construct()
	{
		// error management
		set_error_handler('developpementErrorHandler');
		ini_set('html_errors', 'on');
		ini_set('display_errors', 'on');
		error_reporting(E_ALL | E_STRICT);
		
		$this->_memory = array_fill(0, 10, 0);
		$this->_time = array_fill(0, 10, 0);

		// Usually set in the parent constructor, but here I need it to evaluate init()
		$useSSL = $this->ssl;
		
		$this->_memory[-3] = memory_get_usage();
		$this->_time[-3] = microtime(true);
		$this->init();
		$this->_memory[-2] = memory_get_usage();
		$this->_time[-2] = microtime(true);
		parent::__construct();
		$this->_memory[-1] = memory_get_usage();
		$this->_time[-1] = microtime(true);
	}
	
	public function run()
	{
		$this->_memory[0] = memory_get_usage();
		$this->_time[0] = microtime(true);
		$this->preProcess();
		$this->_memory[1] = memory_get_usage();
		$this->_time[1] = microtime(true);
		$this->setMedia();
		$this->_memory[2] = memory_get_usage();
		$this->_time[2] = microtime(true);
		$this->displayHeader();
		$this->_memory[3] = memory_get_usage();
		$this->_time[3] = microtime(true);
		$this->process();
		$this->_memory[4] = memory_get_usage();
		$this->_time[4] = microtime(true);
		$this->displayContent();
		$this->_memory[5] = memory_get_usage();
		$this->_time[5] = microtime(true);
		$this->displayFooter();
	}
	
	function ini_get_display_errors()
	{
		$a = 'display_errors';
		$b = ini_get($a);
		switch (strtolower($b))
		{
			case 'on':
			case 'yes':
			case 'true':
				return 'assert.active' !== $a;
			case 'stdout':
			case 'stderr':
				return 'display_errors' === $a;
			default:
				return (bool)(int)$b;
		}
	}

	private function sizeofvar($var)
	{
		$start_memory = memory_get_usage();   
		$tmp = unserialize(serialize($var));   
		$size = memory_get_usage() - $start_memory;
		return $size;
	}
	
	public function displayFooter()
	{
		global $start_time;
		parent::displayFooter();
		if (!$this->ini_get_display_errors())
			return;
		
		$this->_memory[6] = memory_get_usage();
		$this->_time[6] = microtime(true);
		
		$hr = '<hr style="color:#F5F5F5;margin:2px" />';

		$totalSize = 0;
		foreach (get_included_files() as $file)
			$totalSize += filesize($file);
			
		$totalQueryTime = 0;
		foreach (Db::getInstance()->queriesTime as $time)
			$totalQueryTime += $time;
			
		$hooktime = Module::getHookTime();
		arsort($hooktime);
		$totalHookTime = 0;
		foreach ($hooktime as $time)
			$totalHookTime += $time;
			
		$globalSize = array();
		$totalGlobalSize = 0;
		foreach ($GLOBALS as $key => $value)
			if ($key != 'GLOBALS')
			{
				$totalGlobalSize += ($size = $this->sizeofvar($value));
				if ($size > 1024)
					$globalSize[$key] = round($size / 1024, 1);
			}
		arsort($globalSize);
			
		echo '<br /><br />
		<div class="rte" style="text-align:left;padding:8px;float:left">
			<b>Load time</b>: '.$this->displayLoadTimeColor($this->_time[6] - $start_time, true).'
			<ul>
				<li>Config: '.$this->displayLoadTimeColor($this->_time[-3] - $start_time).'</li>
				<li>Init: '.$this->displayLoadTimeColor(($this->_time[-2] - $this->_time[-3])).'</li>
				<li>Constructor: '.$this->displayLoadTimeColor(($this->_time[-1] - $this->_time[-2])).'</li>
				<li>preProcess: '.$this->displayLoadTimeColor(($this->_time[1] - $this->_time[0])).'</li>
				<li>setMedia: '.$this->displayLoadTimeColor(($this->_time[2] - $this->_time[1])).'</li>
				<li>displayHeader: '.$this->displayLoadTimeColor(($this->_time[3] - $this->_time[2])).'</li>
				<li>process: '.$this->displayLoadTimeColor(($this->_time[4] - $this->_time[3])).'</li>
				<li>displayContent: '.$this->displayLoadTimeColor(($this->_time[5] - $this->_time[4])).'</li>
				<li>displayFooter: '.$this->displayLoadTimeColor(($this->_time[6] - $this->_time[5])).'</li>
			</ul>
		</div>
		<div class="rte" style="text-align:left;padding:8px;float:left;margin-left:20px">
			<b>Hook processing</b>: '.$this->displayLoadTimeColor($totalHookTime).'
			<ul>';
		foreach ($hooktime as $hook => $time)
			echo '<li>'.$hook.': '.$this->displayLoadTimeColor($time).'</li>';
		echo '</ul>
		</div>
		<div class="rte" style="text-align:left;padding:8px;float:left;margin-left:20px">
			<b>Memory peak usage</b>: '.$this->displayMemoryColor(memory_get_peak_usage()).'
			<ul>
				<li>Config: '.$this->displayMemoryColor($this->_memory[-3]).'</li>
				<li>Init: '.$this->displayMemoryColor(($this->_memory[-2] - $this->_memory[-3])).'</li>
				<li>Constructor: '.$this->displayMemoryColor(($this->_memory[-1] - $this->_memory[-2])).'</li>
				<li>preProcess: '.$this->displayMemoryColor(($this->_memory[1] - $this->_memory[0])).'</li>
				<li>setMedia: '.$this->displayMemoryColor(($this->_memory[2] - $this->_memory[1])).'</li>
				<li>displayHeader: '.$this->displayMemoryColor(($this->_memory[3] - $this->_memory[2])).'</li>
				<li>process: '.$this->displayMemoryColor(($this->_memory[4] - $this->_memory[3])).'</li>
				<li>displayContent: '.$this->displayMemoryColor(($this->_memory[5] - $this->_memory[4])).'</li>
				<li>displayFooter: '.$this->displayMemoryColor(($this->_memory[6] - $this->_memory[5])).'</li>
			</ul>
		</div>';
		
		$countByTypes = '';
		foreach (Db::getInstance()->countTypes as $type => $count)
			if ($count)
				$countByTypes .= '<li>'.$count.' x '.$type.'</li>';
		$countByTypes = rtrim($countByTypes, ' |');
		
		echo '
		<div class="rte" style="text-align:left;padding:8px;float:left;margin-left:20px">
			<b>SQL Queries</b>: '.$this->displaySQLQueries(Db::getInstance()->count).'
			<ul>'.$countByTypes.'</ul>
			<br /><b>Time spent querying</b>: '.$this->displayLoadTimeColor($totalQueryTime).'
		</div>
		<div class="rte" style="text-align:left;padding:8px;float:left;margin-left:20px">
			<b>Included files</b>: '.sizeof(get_included_files()).'<br />
			<b>Size of included files</b>: '.$this->displayMemoryColor($totalSize).'
		</div>
		<div class="rte" style="text-align:left;padding:8px;float:left;margin-left:20px">
			<b>Globals (&gt; 1 Ko only): '.round($totalGlobalSize / 1024).' Ko</b>
			<ul>';
		foreach ($globalSize as $global => $size)
			echo '<li>'.$global.' &asymp; '.$size.' Ko</li>';
		echo '</ul>
		</div>';
		
		echo '
		<div class="rte" style="text-align:left;padding:8px;clear:both;margin-top:20px">
			<ul>
				<li><a href="#stopwatch">Go to Stopwatch</a></li>
				<li><a href="#doubles">Go to Doubles</a></li>
				<li><a href="#tables">Go to Tables</a></li>
			</ul>
		</div>
		<div class="rte" style="text-align:left;padding:8px">
		<h3><a name="stopwatch">Stopwatch (with SQL_NO_CACHE)</a></h3>';
		$queries = Db::getInstance()->queriesTime;
		arsort($queries);
		foreach ($queries as $q => $time)
			echo $hr.'<b '.$this->getTimeColor($time * 1000).'>'.round($time * 1000, 3).' ms</b> '.$q;
		echo '</div>
		<div class="rte" style="text-align:left;padding:8px">
		<h3><a name="doubles">Doubles (IDs replaced by "XX")</a></h3>';
		$queries = Db::getInstance()->queries;
		arsort($queries);
		foreach ($queries as $q => $nb)
			echo $hr.'<b '.$this->getQueryColor($nb).'>'.$nb.'</b> '.$q;
		echo '</div>
		<div class="rte" style="text-align:left;padding:8px">
		<h3><a name="tables">Tables stress</a></h3>';
		$tables = Db::getInstance()->tables;
		arsort($tables);
		foreach ($tables as $table => $nb)
			echo $hr.'<b '.$this->getTableColor($nb).'>'.$nb.'</b> '.$table;
		echo '</div>';
	}
}
