<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class Autoload
{
	/**
	 * File where classes index is stored
	 */
	const INDEX_FILE = 'cache/class_index.php';

	/**
	 * @var Autoload
	 */
	protected static $instance;

	/**
	 * @var string Root directory
	 */
	protected $root_dir;

	/**
	 *  @var array array('classname' => 'path/to/override', 'classnamecore' => 'path/to/class/core')
	 */
	public $index = array();

	protected function __construct()
	{
		$this->root_dir = dirname(dirname(__FILE__)).'/';
		if (file_exists($this->root_dir.Autoload::INDEX_FILE))
			$this->index = include($this->root_dir.Autoload::INDEX_FILE);
	}

	/**
	 * Get instance of autoload (singleton)
	 *
	 * @return Autoload
	 */
	public static function getInstance()
	{
		if (!Autoload::$instance)
			Autoload::$instance = new Autoload();

		return Autoload::$instance;
	}

	/**
	 * Retrieve informations about a class in classes index and load it
	 *
	 * @param string $classname
	 */
	public function load($classname)
	{
		// Smarty uses its own autoloader, so we exclude all Smarty classes
		if (strpos(strtolower($classname), 'smarty_') === 0)
			return;

		// regenerate the class index if the requested class is not found in the index or if the requested file doesn't exists
		if (!isset($this->index[$classname])
			|| ($this->index[$classname] && !is_file($this->root_dir.$this->index[$classname]))
			|| (isset($this->index[$classname.'Core']) && $this->index[$classname.'Core'] && !is_file($this->root_dir.$this->index[$classname.'Core'])))
			$this->generateIndex();

		// If $classname has not core suffix (E.g. Shop, Product)
		if (substr($classname, -4) != 'Core')
		{
			// If requested class does not exist, load associated core class
			if (isset($this->index[$classname]) && !$this->index[$classname])
			{
				require($this->root_dir.$this->index[$classname.'Core']);

				// Since the classname does not exists (we only have a classCore class), we have to emulate the declaration of this class
				$class_infos = new ReflectionClass($classname.'Core');
				eval(($class_infos->isAbstract() ? 'abstract ' : '').'class '.$classname.' extends '.$classname.'Core {}');
			}
			else
			{
				// request a non Core Class load the associated Core class if exists
				if (isset($this->index[$classname.'Core']))
					require_once($this->root_dir.$this->index[$classname.'Core']);
				if (isset($this->index[$classname]))
					require_once($this->root_dir.$this->index[$classname]);
			}
		}
		// Call directly ProductCore, ShopCore class
		else
			require($this->root_dir.$this->index[$classname]);
	}

	/**
	 * Generate classes index
	 */
	public function generateIndex()
	{
		$classes = array_merge(
			$this->getClassesFromDir('classes/'),
			$this->getClassesFromDir('override/classes/'),
			$this->getClassesFromDir('controllers/'),
			$this->getClassesFromDir('override/controllers/')
		);
		ksort($classes);
		$content = '<?php return '.var_export($classes, true).'; ?>';

		// Write classes index on disc to cache it
		$filename = $this->root_dir.Autoload::INDEX_FILE;
		if ((file_exists($filename) && !is_writable($filename)) || !is_writable(dirname($filename)))
		{
			header('HTTP/1.1 503 temporarily overloaded');
			// Cannot use PrestaShopException in this context
			die('/cache/class_index.php is not writable, please give write permissions (chmod 666) on this file.');
		}
		else
		{
			// Let's write index content in cache file
			// In order to be sure that this file is correctly written, a check is done on the file content
			$loop_protection = 0;
			do
			{
				$integrity_is_ok = false;
				file_put_contents($filename, $content, LOCK_EX);
				if ($loop_protection++ > 10)
					break;

				// If the file content end with PHP tag, integrity of the file is ok
				if (preg_match('#\?>\s*$#', file_get_contents($filename)))
					$integrity_is_ok = true;
			}
			while (!$integrity_is_ok);

			if (!$integrity_is_ok)
			{
				file_put_contents($filename, '<?php return array(); ?>', LOCK_EX);
				// Cannot use PrestaShopException in this context
				die('Your file '.$filename.' is corrupted. Please remove this file, a new one will be regenerated automatically');
			}
		}

		$this->index = $classes;
	}

	/**
	 * Retrieve recursively all classes in a directory and its subdirectories
	 *
	 * @param string $path Relativ path from root to the directory
	 * @return array
	 */
	protected function getClassesFromDir($path)
	{
		$classes = array();

		foreach (scandir($this->root_dir.$path) as $file)
		{
			if ($file[0] != '.')
			{
				if (is_dir($this->root_dir.$path.$file))
					$classes = array_merge($classes, $this->getClassesFromDir($path.$file.'/'));
				else if (substr($file, -4) == '.php')
				{
					$content = file_get_contents($this->root_dir.$path.$file);
			 		$pattern = '#\W((abstract\s+)?class|interface)\s+(?P<classname>'.basename($file, '.php').'(Core)?)'
			 					.'(\s+extends\s+[a-z][a-z0-9_]*)?(\s+implements\s+[a-z][a-z0-9_]*(\s*,\s*[a-z][a-z0-9_]*)*)?\s*\{#i';
			 		if (preg_match($pattern, $content, $m))
			 		{
			 			$classes[$m['classname']] = $path.$file;
						if (substr($m['classname'], -4) == 'Core')
							$classes[substr($m['classname'], 0, -4)] = '';
			 		}
				}
			}
		}

		return $classes;
	}

	public function getClassPath($classname)
	{
		return isset($this->index[$classname]) ? $this->index[$classname] : null;
	}
}

