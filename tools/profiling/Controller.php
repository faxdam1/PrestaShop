<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
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
			return '<span style="color:red">'.round($n, 2).' Mb</span>';
		if ($n > 1)
			return '<span style="color:orange">'.round($n, 2).' Mb</span>';
		return '<span style="color:green">'.round($n, 2).' Mb</span>';
	}

	private function displayPeakMemoryColor($n)
	{
		$n /= 1048576;
		if ($n > 16)
			return '<span style="color:red">'.round($n, 1).' Mb</span>';
		if ($n > 12)
			return '<span style="color:orange">'.round($n, 1).' Mb</span>';
		return '<span style="color:green">'.round($n, 1).' Mb</span>';
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
		//set_error_handler('developpementErrorHandler');
		ini_set('html_errors', 'on');
		ini_set('display_errors', 'on');
		error_reporting(E_ALL | E_STRICT);

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

			if (!empty($this->redirect_after))
				$this->redirect();

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
				$action = Tools::getValue('action');
				if (!empty($action) && method_exists($this, 'displayAjax'.Tools::toCamelCase($action))) 
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

		if (!$this->ini_get_display_errors())
			return;

		$memory_peak_usage = memory_get_peak_usage();
			
		$hr = '<hr style="color:#F5F5F5;margin:2px" />';

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
					$globalSize[$key] = round($size / 1024, 1);
			}
		arsort($globalSize);

		$cache = Cache::retrieveAll();
 	 	$totalCacheSize = $this->sizeofvar($cache);

		echo '
		<div style="clear:both;height:20px;line-height:20px">&nbsp;</div>
		<div style="margin:50px;background-color:#FFFFFF">
		<div class="rte" style="text-align:left;padding:8px;float:left">
			<b>Load time</b>: '.$this->displayLoadTimeColor($this->_time['display'] - $start_time, true).'';
		if (self::$_footer)
			echo '<ul>';
			$last_time = $start_time;
			foreach ($this->_time as $k => $time)
			{
				echo '<li>'.$k.': '.$this->displayLoadTimeColor($time - $last_time).'</li>';
				$last_time = $time;
			}
			echo '</ul>';
		echo '</div>
		
		<div class="rte" style="text-align:left;padding:8px;float:left;margin-left:20px">
			<b>Hook processing</b>: '.$this->displayLoadTimeColor($totalHookTime).' / '.$this->displayMemoryColor($totalHookMemoryUsage).'<br />
			'.(int)count($executedModules).' methods called in '.(int)count(array_unique($executedModules)).' modules
			<ul>';
		foreach ($hooktime as $hook => $time)
			echo '<li>'.$hook.': '.$this->displayLoadTimeColor($time).' / '.$this->displayMemoryColor($hookMemoryUsage[$hook]).'</li>';
		echo '</ul>
		</div>
		<div class="rte" style="text-align:left;padding:8px;float:left;margin-left:20px">
			<b>Memory peak usage</b>: '.$this->displayPeakMemoryColor($memory_peak_usage);
		if (self::$_footer)
		{
			echo '<ul>';
			$last_memory = 0;
			foreach ($this->_memory as $k => $memory)
			{
				echo '<li>'.$k.': '.$this->displayMemoryColor($memory - $last_memory).' ('.$this->displayPeakMemoryColor($this->_mempeak[$k]).')</li>';
				$last_memory = $memory;
			}
			echo '</ul>';
		}
		echo '<br /><br />
 	 	<b>Total cache size (in Cache class)</b>: '.$this->displayMemoryColor($totalCacheSize).'
 	 	</div>';

		echo '
		<div class="rte" style="text-align:left;padding:8px;float:left;margin-left:20px">
			<b>DB type</b>: '.get_class(Db::getInstance()).'
			<br /><b>SQL Queries</b>: '.$this->displaySQLQueries(count(Db::getInstance()->queries)).'
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

		echo '
		<div class="rte" style="text-align:left;padding:8px;clear:both;margin-top:20px">
			<ul>
				<li><a href="#stopwatch">Go to Stopwatch</a></li>
				<li><a href="#doubles">Go to Doubles</a></li>
				<li><a href="#tables">Go to Tables</a></li>
				'.(isset(ObjectModel::$debug_list) ? '<li><a href="#objectModels">Go to ObjectModels</a></li>' : '').'
				<li><a onclick="$(\'#queries_table\').toggle();" style="cursor:pointer">Display queries table</a></li>
				<li><a href="#includedFiles">Go to files</a></li>
			</ul>
		</div>
		<div id="queries_table" style="display:none;margin:4px">
			<table class="table std">
				<tr><th>Time (ms)</th><th>Rows</th><th>Query</th><th>Location</th><th>Filesort</th><th>Group By</th></tr>';
		foreach ($array_queries as &$data)
		{
			$data['location'] = str_replace('\\', '/', substr($data['location'], strlen(_PS_ROOT_DIR_)));
			$data['query'] = str_replace('SQL_NO_CACHE ', '', $data['query']);
			echo '<tr><td>'.round(1000 * $data['time'], 3).'</td><td>'.$data['rows'].'</td><td>'.$data['query'].'</td><td>'.$data['location'].'</td><td>'.($data['filesort'] ? 'Yes' : '').'</td><td>'.($data['group_by'] ? 'Yes' : '').'</td></tr>';
		}
		echo '
			</table>
		</div>
		<div class="rte" style="text-align:left;padding:8px">
		<h3><a name="stopwatch">Stopwatch (with SQL_NO_CACHE) (total = '.count(Db::getInstance()->queries).')</a></h3>';
		$i = 1;
		foreach ($array_queries as $data)
		{
			$echo_stack = '';
			array_shift($data['stack']);
			foreach ($data['stack'] as $call)
				$echo_stack .= 'from '.str_replace('\\', '/', substr($call['file'], strlen(_PS_ROOT_DIR_))).':'.$call['line'].'<br />';

			echo $hr.'<div onclick="$(\'#qbt'.$i.'\').toggle();"><b '.$this->getTimeColor($data['time'] * 1000).'>'.round($data['time'] * 1000, 3).' ms</b>
			'.htmlspecialchars($data['query'], ENT_NOQUOTES, 'utf-8', false).'<br />
			in '.$data['location'].'<br />
			<div id="qbt'.($i++).'" style="display:none">'.$echo_stack.'</div>';
			if (preg_match('/^\s*select\s+/i', $data['query']))
			{
				if ($data['filesort'])
					echo '<b '.$this->getTimeColor($data['time'] * 1000).'>USING FILESORT</b> - ';
				echo $this->displayRowsBrowsed($data['rows']);
				if ($data['group_by'])
					echo '<br /><b>Useless GROUP BY need to be removed</b>';
			}
			echo '</div>';
		}
		$queries = Db::getInstance()->uniqQueries;
		arsort($queries);
		$count = count(Db::getInstance()->uniqQueries);
		foreach ($queries as $q => &$nb)
		if ($nb == 1)
			$count--;
		if ($count)
			echo '</div>
			<div class="rte" style="text-align:left;padding:8px">
			<h3><a name="doubles">Doubles (IDs replaced by "XX") (total = '.$count.')</a></h3>';
		foreach ($queries as $q => $nb)
			if ($nb > 1)
				echo $hr.'<b '.$this->getQueryColor($nb).'>'.$nb.'</b> '.$q;
		echo '</div>
		<div class="rte" style="text-align:left;padding:8px">
		<h3><a name="tables">Tables stress</a></h3>';
		$tables = Db::getInstance()->tables;
		arsort($tables);
		foreach ($tables as $table => $nb)
			echo $hr.'<b '.$this->getTableColor($nb).'>'.$nb.'</b> '.$table;
		echo '</div>';

		if (isset(ObjectModel::$debug_list))
		{
			echo '<div class="rte" style="text-align:left;padding:8px">
			<h3><a name="objectModels">ObjectModel instances</a></h3>';
			$list = ObjectModel::$debug_list;
			uasort($list, create_function('$a,$b', 'return (count($a) < count($b)) ? 1 : -1;'));
			$i = 0;
			foreach ($list as $class => $info)
			{
				echo $hr.'<b '.$this->getObjectModelColor(count($info)).'>'.count($info).'</b> ';
				echo '<a href="#" onclick="$(\'#object_model_'.$i.'\').css(\'display\', $(\'#object_model_'.$i.'\').css(\'display\') == \'none\' ? \'block\' : \'none\'); return false">'.$class.'</a>';
				echo '<div id="object_model_'.$i.'" style="display: none">';
				foreach ($info as $trace)
					echo ltrim(str_replace(array(_PS_ROOT_DIR_, '\\'), array('', '/'), $trace['file']), '/').' ['.$trace['line'].']<br />';
				echo '</div>';
				$i++;
			}
			echo '</div>';
		}

		// List of included files
		echo '<div class="rte" style="text-align:left;padding:8px">
		<h3><a name="includedFiles">Included files</a></h3>';
		$i = 1;
		foreach (get_included_files() as $file)
		{
			$file = ltrim(str_replace('\\', '/', str_replace(_PS_ROOT_DIR_, '', $file)), '/');
			$file = '<b style="color: red">'.dirname($file).'/</b><b style="color: blue">'.basename($file).'</b>';
			echo $i.' '.$file.'<br />';
			$i++;
		}
		echo '</div>
		<div style="clear:both;height:20px;line-height:20px">&nbsp;</div>
		</div>';
	}
}

function prestashop_querytime_sort($a, $b)
{
	if ($a['time'] == $b['time'])
		return 0;
	return ($a['time'] > $b['time']) ? -1 : 1;
}
