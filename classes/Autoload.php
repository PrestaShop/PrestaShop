<?php

class Autoload
{
	const INDEX_FILE = 'cache/class_index.php';

	protected static $instance;
	protected $root_dir;

	/**
	 *  array('classname' => 'path/to/override', 'classnamecore' => 'path/to/class/core')
	 */
	public $index = array();


	protected function __construct()
	{
		$this->root_dir = dirname(dirname(__FILE__)).'/';
	}

	/**
	 *
	 */
	public static function getInstance()
	{
		if (!Autoload::$instance)
			Autoload::$instance = new Autoload();

		return Autoload::$instance;
	}

	public function generateIndex()
	{
		$classes = array_merge(
							$this->getClassesFromDir('classes/', true),
							$this->getClassesFromDir('override/classes/', false)
						);
		$content = '<?php return '.var_export($classes, true).';';

		$filename = $this->root_dir.Autoload::INDEX_FILE;

		if ((file_exists($filename) && is_writable($filename)) || is_writable(dirname($filename)))
			file_put_contents($filename, $content);
		else
			throw new Exception($filename.' is not writable!');

		$this->index = include_once($this->root_dir.Autoload::INDEX_FILE);
	}

	protected function getClassesFromDir($path, $is_core)
	{
		$classes = array();

		foreach (scandir($this->root_dir.$path) as $file)
		{
			if ($file[0] != '.')
			{
				if (is_dir($this->root_dir.$path.$file))
					$classes = array_merge($classes, $this->getClassesFromDir($path.$file.'/', $is_core));
				else if (substr($file, -4) == '.php')
			 	{
			 		$content = file_get_contents($path.$file);
			 		if (preg_match('#\W((abstract\s+)?class|interface)\s+(?P<classname>'.basename($file, '.php').'(Core)?)\s+((extends|implements)\s+[a-z][a-z0-9_]*)?\s*\{#i', $content, $m))
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

	/**
	 *
	 *
	 * @param string $classname
	 */
	public function load($classname)
	{
		if (!isset($this->index[$classname]))
			$this->generateIndex();

		$is_core = (substr($classname, -4) == 'Core');

		// If $classname has not core suffix (E.g. Shop, Product)
		if (!$is_core)
		{
			// If requested class does not exist, load associated core class
		 	if (isset($this->index[$classname]) && !$this->index[$classname])
		 	{
				require_once($this->root_dir.$this->index[$classname.'Core']);
		 		if (file_exists($this->root_dir.'override/'.$this->index[$classname.'Core']))
		 		{
		 			$this->generateIndex();
		 			require_once($this->root_dir.$this->index[$classname]);
		 		}
		 		else
		 		{
					// Since the classname does not exists (we only have a classCore class), we have to emulate the declaration of this class
					$class_infos = new ReflectionClass($classname.'Core');
					eval(($class_infos->isAbstract() ? 'abstract ' : '').'class '.$classname.' extends '.$classname.'Core {}');
				}
			}
			else
			{
				// request a non Core Class load the associated Core class if exists
				if (isset($this->index[$classname.'Core']))
					require_once($this->root_dir.$this->index[$classname.'Core']);

				require_once($this->root_dir.$this->index[$classname]);
			}
		}
		// Call directly ProductCore, ShopCore class
		else
			require_once($this->root_dir.$this->index[$classname]);

		if (!class_exists($classname, false) && !interface_exists($classname, false))
			throw new Exception('Class not found: '.$classname);
	}

}

