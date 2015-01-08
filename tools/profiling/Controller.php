<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class Controller extends ControllerCore
{
	public $_memory = array();
	public $_time = array();
	private static $_footer = true;

	public static function disableParentCalls()
	{
		self::$_footer = false;
	}

	private function displayMemoryColor($n)
	{
		$n /= 1048576;
		if ($n > 3)
			return '<span style="color:red">'.sprintf('%0.2f', $n).'</span>';
		elseif ($n > 1)
			return '<span style="color:orange">'.sprintf('%0.2f', $n).'</span>';
		elseif (round($n, 2) > 0)
			return '<span style="color:green">'.sprintf('%0.2f', $n).'</span>';
		return '<span style="color:green">-</span>';
	}

	private function displayPeakMemoryColor($n)
	{
		$n /= 1048576;
		if ($n > 16)
			return '<span style="color:red">'.sprintf('%0.1f', $n).'</span>';
		if ($n > 12)
			return '<span style="color:orange">'.sprintf('%0.1f', $n).'</span>';
		return '<span style="color:green">'.sprintf('%0.1f', $n).'</span>';
	}

	private function displaySQLQueries($n)
	{
		if ($n > 150)
			return '<span style="color:red">'.$n.' queries</span>';
		if ($n > 100)
			return '<span style="color:orange">'.$n.' queries</span>';
		return '<span style="color:green">'.$n.' quer'.($n == 1 ? 'y' : 'ies').'</span>';
	}

	private function displayRowsBrowsed($n)
	{
		if ($n > 400)
			return '<span style="color:red">'.$n.' rows browsed</span>';
		if ($n > 100)
			return '<span style="color:orange">'.$n.'  rows browsed</span>';
		return '<span style="color:green">'.$n.' row'.($n == 1 ? '' : 's').' browsed</span>';
	}

	private function displayLoadTimeColor($n, $kikoo = false)
	{
		if ($n > 1)
			return '<span style="color:red">'.round($n * 1000).'</span>'.($kikoo ? ' ms<br />You\'d better run your shop on a toaster' : '');
		elseif ($n > 0.5)
			return '<span style="color:orange">'.round($n * 1000).'</span>'.($kikoo ? ' ms<br />I hope it is a shared hosting' : '');
		elseif ($n > 0)
			return '<span style="color:green">'.round($n * 1000).'</span>'.($kikoo ? ' ms<br />Good boy! That\'s what I call a webserver!' : '');
		return '<span style="color:green">-</span>'.($kikoo ? ' ms<br />Faster than light' : '');
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

	private function getObjectModelColor($n)
	{
		if ($n > 50)
			return 'style="color:red"';
		if ($n > 10)
			return 'style="color:orange"';
		return 'style="color:green"';
	}

	public function __construct()
	{
		if (!self::$_footer)
			return;

		$this->_memory['config'] = memory_get_usage();
		$this->_mempeak['config'] = memory_get_peak_usage();
		$this->_time['config'] = microtime(true);

		parent::__construct();
		$this->_memory['constructor'] = memory_get_usage();
		$this->_mempeak['constructor'] = memory_get_peak_usage();
		$this->_time['constructor'] = microtime(true);
	}

	public function run()
	{
		$this->init();
		$this->_memory['init'] = memory_get_usage();
		$this->_mempeak['init'] = memory_get_peak_usage();
		$this->_time['init'] = microtime(true);

		if ($this->checkAccess())
		{
			$this->_memory['checkAccess'] = memory_get_usage();
			$this->_mempeak['checkAccess'] = memory_get_peak_usage();
			$this->_time['checkAccess'] = microtime(true);

			if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className)))
				$this->setMedia();
			$this->_memory['setMedia'] = memory_get_usage();
			$this->_mempeak['setMedia'] = memory_get_peak_usage();
			$this->_time['setMedia'] = microtime(true);

			// postProcess handles ajaxProcess
			$this->postProcess();
			$this->_memory['postProcess'] = memory_get_usage();
			$this->_mempeak['postProcess'] = memory_get_peak_usage();
			$this->_time['postProcess'] = microtime(true);

			if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className)))
				$this->initHeader();
			$this->_memory['initHeader'] = memory_get_usage();
			$this->_mempeak['initHeader'] = memory_get_peak_usage();
			$this->_time['initHeader'] = microtime(true);

			$this->initContent();
			$this->_memory['initContent'] = memory_get_usage();
			$this->_mempeak['initContent'] = memory_get_peak_usage();
			$this->_time['initContent'] = microtime(true);

			if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className)))
				$this->initFooter();
			$this->_memory['initFooter'] = memory_get_usage();
			$this->_mempeak['initFooter'] = memory_get_peak_usage();
			$this->_time['initFooter'] = microtime(true);

			// default behavior for ajax process is to use $_POST[action] or $_GET[action]
			// then using displayAjax[action]
			if ($this->ajax)
			{
				$action = Tools::toCamelCase(Tools::getValue('action'), true);
				if (!empty($action) && method_exists($this, 'displayAjax'.$action)) 
					$this->{'displayAjax'.$action}();
				elseif (method_exists($this, 'displayAjax'))
					$this->displayAjax();
			}
			else
				$this->displayDebug();
		}
		else
		{
			$this->initCursedPage();
			$this->displayDebug();
		}
	}

	private function sizeofvar($var)
	{
		$start_memory = memory_get_usage();
		try {
			$tmp = Tools::unSerialize(serialize($var));
		} catch (Exception $e) {
			$tmp = $this->getVarData($var);
		}
		$size = memory_get_usage() - $start_memory;
		return $size;
	}
	
	private function getVarData($var)
	{
		if (is_object($var))
			return $var;
		return (string)$var;
	}

	public function displayDebug()
	{
		global $start_time;

		$this->display();
		$this->_memory['display'] = memory_get_usage();
		$this->_mempeak['display'] = memory_get_peak_usage();
		$this->_time['display'] = microtime(true);

		$memory_peak_usage = memory_get_peak_usage();
			
		$hr = '<hr>';

		$totalSize = 0;
		foreach (get_included_files() as $file)
			$totalSize += filesize($file);

		$totalQueryTime = 0;
		foreach (Db::getInstance()->queries as $data)
			$totalQueryTime += $data['time'];

		$executedModules = Hook::getExecutedModules();
		$hooktime = Hook::getHookTime();
		arsort($hooktime);
		$totalHookTime = 0;
		foreach ($hooktime as $time)
			$totalHookTime += $time;

		$hookMemoryUsage = Hook::getHookMemoryUsage();
		arsort($hookMemoryUsage);
		$totalHookMemoryUsage = 0;
		foreach ($hookMemoryUsage as $usage)
			$totalHookMemoryUsage += $usage;

		$globalSize = array();
		$totalGlobalSize = 0;
		foreach ($GLOBALS as $key => $value)
			if ($key != 'GLOBALS')
			{
				$totalGlobalSize += ($size = $this->sizeofvar($value));
				if ($size > 1024)
					$globalSize[$key] = round($size / 1024);
			}
		arsort($globalSize);

		$cache = Cache::retrieveAll();
 	 	$totalCacheSize = $this->sizeofvar($cache);

		echo '
		<style>
			#ps_profiling{
				padding: 20px;
			}
			.ps_back-office.page-sidebar #ps_profiling{
				margin-left: 210px;
			}
			.ps_back-office.page-sidebar-closed #ps_profiling{
				margin-left: 50px;
			}
			.ps_back-office #ps_profiling{
				clear: both;
				padding: 10px;
				margin-bottom: 50px;
			}
			#ps_profiling *{
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				color: #888;
			}
			#ps_profiling .ps_profiling_title{
				font-size: 20px;
				display: inline-block;
				padding-bottom: 15px;
			}
			#ps_profiling ul{
				margin: 0;
				padding: 0;
				list-style: none;
			}
			#ps_profiling hr{
				margin: 5px 0;
				padding: 0;
				border: none;
				border-bottom: solid 1px #ccc;
			}
			#ps_profiling td pre{
				padding: 6px;
				margin-right: 10px;
				border-radius: 5px;
				overflow: auto;
				display: block;
				color: #777;
				font-size: 12px;
				line-height: 1.42857;
				word-break: break-all;
				word-wrap: break-word;
				background-color: whitesmoke;
				border: 1px solid #cccccc;
				max-width: 960px;
			}
			#ps_profiling td .qbt{
				max-height: 140px;
				overflow: auto;
			}
			#ps_profiling table{
				width: 100%;
				margin-bottom: 10px;
				background-color: white;
			}
			#ps_profiling table th{
				font-weight: normal;
				border-bottom: 1px solid #999;
				color: #888;
				padding: 5px 0;
			}
			#ps_profiling table td{
				border-bottom: 1px solid #eee;
				padding: 6px;
			}

			.sortable thead th{
				cursor:pointer;
			}
			#ps_profiling table .text-right{
				text-align: right
			}
			#ps_profiling table .text-left{
				text-align: left
			}
			#ps_profiling table .text-center{
				text-align: center
			}
			#ps_profiling .ps_profiling_row{
				clear: both;
				margin-bottom: 60px;
			}
			#ps_profiling .ps_profiling_col4{
				float: left;
				padding: 0 10px;
				border-right: 1px solid #ccc;
				width: 25%;
			}
			@media (max-width: 1200px) {
				#ps_profiling .ps_profiling_col4 {
					width: 50%;
				}
			}
			@media (max-width: 600px) {
				#ps_profiling .ps_profiling_col4 {
					width: 100%;
				}
			}
			#ps_profiling .ps_profiling_col4:last-child{
				border-right: none;
			}
			#ps_profiling .ps_profiling_infobox{
				background-color: white;
				padding: 5px 10px;
				border: 1px solid #ccc;
				margin-bottom: 10px;
			}
			#ps_profiling .text-muted{
				color: #bbb;
			}
		</style>';

		echo '<div id="ps_profiling">';
		if (!empty($this->redirect_after))
			echo '<div class="ps_profiling_row"><div class="ps_profiling_col12"><h2>Caught redirection to <a href="'.htmlspecialchars($this->redirect_after).'">'.htmlspecialchars($this->redirect_after).'</a></h2></div></div>';
		
		echo '
		<div class="ps_profiling_row">
		<div class="ps_profiling_col4">
			<div class="ps_profiling_infobox"><b>Load time</b>: '.$this->displayLoadTimeColor($this->_time['display'] - $start_time, true).'</div>';

		if (self::$_footer){
				echo '<table>';
				echo '<thead><tr><th class="text-left">Execution</th><th class="text-right">Load time (ms)</th></tr><thead><tbody>';
				$last_time = $start_time;
				foreach ($this->_time as $k => $time)
				{
					echo '<tr><td>'.$k.'</td><td class="text-right">'.$this->displayLoadTimeColor($time - $last_time).'</td></tr>';
					$last_time = $time;
				}
				echo '</tbody></table>';
			}
		echo '</div>
		<div class="ps_profiling_col4">
			<div class="ps_profiling_infobox"><b>Hook processing</b>: '.$this->displayLoadTimeColor($totalHookTime).' ms / '.$this->displayMemoryColor($totalHookMemoryUsage).' Mb<br>
			'.(int)count($executedModules).' methods called in '.(int)count(array_unique($executedModules)).' modules</div>';
			echo '<table>';
			echo '<thead><tr><th class="text-left">Hook</th><th class="text-right">Processing</th></tr><thead><tbody>';
		foreach ($hooktime as $hook => $time)
			echo '<tr><td>'.$hook.'</td><td class="text-right">'.$this->displayMemoryColor($hookMemoryUsage[$hook]).' Mb in '.$this->displayLoadTimeColor($time).' ms</td></tr>';
		echo '</table>
		</div>

		<div class="ps_profiling_col4">
			<div class="ps_profiling_infobox"><b>Memory peak usage</b>: '.$this->displayPeakMemoryColor($memory_peak_usage).' Mb</div>';
		if (self::$_footer)
		{
			echo '<table>';
			echo '<thead><tr><th class="text-left">Execution</th><th class="text-right">Memory (Mb)</th><th class="text-right">Total (Mb)</th></tr><thead><tbody>';
			$last_memory = 0;
			foreach ($this->_memory as $k => $memory)
			{
				echo '<tr><td>'.$k.'</td><td class="text-right">'.$this->displayMemoryColor($memory - $last_memory).'</td><td class="text-right">'.$this->displayPeakMemoryColor($this->_mempeak[$k]).'</td></tr>';
				$last_memory = $memory;
			}
			echo '<tbody></table>';
		}
		echo '
 	 	</div>';

		$compile = array(
			0 => 'green">never recompile',
			1 => 'orange">auto',
			2 => 'red">force compile'
		);
		
		echo '
		<div class="ps_profiling_col4">
			<div class="ps_profiling_infobox"><b>Total cache size in Cache class</b>: '.$this->displayMemoryColor($totalCacheSize).' Mb</div>
			<div class="ps_profiling_infobox"><b>Smarty cache</b>: <span style="color:'.(Configuration::get('PS_SMARTY_CACHE') ? 'green">enabled' : 'red">disabled').'</span></div>
			<div class="ps_profiling_infobox"><b>Smarty compilation</b>: <span style="color:'.$compile[Configuration::get('PS_SMARTY_FORCE_COMPILE')].'</span></div>
			<div class="ps_profiling_infobox"><b>SQL Queries</b>: '.$this->displaySQLQueries(count(Db::getInstance()->queries)).' in '.$this->displayLoadTimeColor($totalQueryTime).' ms</div>
			<div class="ps_profiling_infobox"><b>Included files</b>: '.sizeof(get_included_files()).' ('.$this->displayMemoryColor($totalSize).' Mb)</div>
			<div class="ps_profiling_infobox"><b>Global vars</b> : '.$this->displayMemoryColor($totalGlobalSize).' Mb
				<ul>';
			foreach ($globalSize as $global => $size)
				echo '<li>$'.$global.' &asymp; '.$size.'k</li>';
			echo '</ul>
			</div>
		</div>';

		$array_queries = array();
		$queries = Db::getInstance()->queries;
		uasort($queries, 'prestashop_querytime_sort');
		foreach ($queries as $data)
		{
			$query_row = array(
				'time' => $data['time'],
				'query' => $data['query'],
				'location' => $data['stack'][0]['file'].':'.$data['stack'][0]['line'],
				'filesort' => false,
				'rows' => 1,
				'group_by' => false,
				'stack' => $data['stack']
			);
			if (preg_match('/^\s*select\s+/i', $data['query']))
			{
				$explain = Db::getInstance()->executeS('explain '.$data['query']);
				if (stristr($explain[0]['Extra'], 'filesort'))
					$query_row['filesort'] = true;
				foreach ($explain as $row)
					$query_row['rows'] *= $row['rows'];
				if (stristr($data['query'], 'group by') && !preg_match('/(avg|count|min|max|group_concat|sum)\s*\(/i', $data['query']))
					$query_row['group_by'] = true;
			}
			$array_queries[] = $query_row;
		}
		echo '</div>';
		echo '
		<div class="ps_profiling_row">
			<ul>
				<li><a href="#stopwatch">Stopwatch</a></li>
				<li><a href="#doubles">Doubles</a></li>
				<li><a href="#tables">Tables stress</a></li>
				'.(isset(ObjectModel::$debug_list) ? '<li><a href="#objectModels">ObjectModel instances</a></li>' : '').'
				<li><a href="#includedFiles">Files included</a></li>
			</ul>
		</div>

		<div class="ps_profiling_row">
		<span class="ps_profiling_title"><a name="stopwatch">Stopwatch (with SQL_NO_CACHE) (total = '.count(Db::getInstance()->queries).')</a></span>';
		$i = 1;
		echo '
		<script type="text/javascript" src="https://raw.githubusercontent.com/drvic10k/bootstrap-sortable/master/Scripts/bootstrap-sortable.js"></script>
		<table class="table table-striped table-condensed sortable">
			<thead>
				<tr>
					<th class="text-left col-lg-6">Query</th>
					<th class="text-left col-lg-1">Time (ms)</th>
					<th class="text-left col-lg-1">Rows</th>
					<th class="text-left col-lg-1">Filesort</th>
					<th class="text-left col-lg-1">Group By</th>
					<th class="text-left col-lg-2">Location</th>
				</tr>
			<thead>
			<tbody>';
		foreach ($array_queries as $data)
		{
			$echo_stack = '';
			array_shift($data['stack']);
			foreach ($data['stack'] as $call)
				$echo_stack .= 'from '.str_replace('\\', '/', substr($call['file'], strlen(_PS_ROOT_DIR_))).':'.$call['line'].'<br />';
			echo '
			<tr>
				<td><pre>'.preg_replace("/(^[\s]*)/m", "", htmlspecialchars($data['query'], ENT_NOQUOTES, 'utf-8', false)).'</pre></td>
				<td><span '.$this->getTimeColor($data['time'] * 1000).'>'.(round($data['time'] * 1000, 1) < 0.1 ? '< 1' : round($data['time'] * 1000, 1)).'</span></td>
				<td>'.$data['rows'].'</td>
				<td>'.($data['filesort'] ? '<span style="color:red">Yes</span>' : '').'</td>
				<td>'.($data['group_by'] ? '<span style="color:red">Yes</span>' : '').'</td>
				<td>in '.$data['location'].'<br /><div class="qbt" id="qbt'.($i++).'">'.$echo_stack.'</div></td>
			</tr>';
		}
		echo '</table>';
		$queries = Db::getInstance()->uniqQueries;
		arsort($queries);
		$count = count(Db::getInstance()->uniqQueries);
		foreach ($queries as $q => &$nb)
		if ($nb == 1)
			$count--;
		if ($count)
			echo '</div>
			<div class="ps_profiling_row">
			<span class="ps_profiling_title"><a name="doubles">Doubles (IDs replaced by "XX") (total = '.$count.')</a></span>
			<table>';
		foreach ($queries as $q => $nb)
			if ($nb > 1)
				echo '<tr><td><span '.$this->getQueryColor($nb).'>'.$nb.'</span> '.$q.'</td></tr>';
		echo '</table></div>

		<div class="ps_profiling_row">
		<span class="ps_profiling_title"><a name="tables">Tables stress</a></span>
		<table>';
		$tables = Db::getInstance()->tables;
		arsort($tables);
		foreach ($tables as $table => $nb)
			echo '<tr><td><span '.$this->getTableColor($nb).'>'.$nb.'</span> '.$table.'</td></tr>';
		echo '</table></div>';

		if (isset(ObjectModel::$debug_list))
		{
			echo '<div class="ps_profiling_row">
			<span class="ps_profiling_title"><a name="objectModels">ObjectModel instances</a></span>';
			$list = ObjectModel::$debug_list;
			uasort($list, create_function('$a,$b', 'return (count($a) < count($b)) ? 1 : -1;'));
			$i = 0;
			echo '<table><thead><tr><th class="text-left">Name</th><th class="text-left">Instance</th><th class="text-left">Source</th></tr></thead><tbody>';
			foreach ($list as $class => $info)
			{
				echo '<tr><td>'.$class.'</td>';
				echo '<td><span '.$this->getObjectModelColor(count($info)).'>'.count($info).'</span></td>';
				echo '<td><div id="object_model_'.$i.'">';
				foreach ($info as $trace)
					echo ltrim(str_replace(array(_PS_ROOT_DIR_, '\\'), array('', '/'), $trace['file']), '/').' ['.$trace['line'].']<br />';
				echo '</div></td></tr>';
				$i++;
			}
			echo '</tbody></table></div>';
		}

		// List of included files
		echo '<div class="ps_profiling_row">
		<span class="ps_profiling_title"><a name="includedFiles">Included files</a></span>
		<table>';
		$i = 1;
		echo '<thead><tr><th class="text-left">#</th><th class="text-left">Filename</th></tr></thead><tbody>';
		foreach (get_included_files() as $file)
		{
			$file = ltrim(str_replace('\\', '/', str_replace(_PS_ROOT_DIR_, '', $file)), '/');
			$file = '<span class="text-muted">'.dirname($file).'/</span><span>'.basename($file).'</span>';
			echo '<tr><td>'.$i.'</td><td>'.$file.'</td></tr>';
			$i++;
		}
		echo '</tbody></table></div></div>';
	}
}

function prestashop_querytime_sort($a, $b)
{
	if ($a['time'] == $b['time'])
		return 0;
	return ($a['time'] > $b['time']) ? -1 : 1;
}
