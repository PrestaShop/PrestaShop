<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class PrestaShopAutoload.
 *
 * @since 1.5
 */
class PrestaShopAutoload
{
    /**
     * @var PrestaShopAutoload
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

    public $_include_override_path = true;

    protected static $class_aliases = array(
        'Collection' => 'PrestaShopCollection',
        'Autoload' => 'PrestaShopAutoload',
        'Backup' => 'PrestaShopBackup',
        'Logger' => 'PrestaShopLogger',
    );

    protected function __construct()
    {
        $this->root_dir = _PS_CORE_DIR_ . '/';
        $file = PrestaShopAutoload::getCacheFileIndex();
        $stubFile = PrestaShopAutoload::getStubFileIndex();
        if (@filemtime($file) && is_readable($file) && @filemtime($stubFile) && is_readable($stubFile)) {
            $this->index = include $file;
        } else {
            $this->generateIndex();
        }
    }

    /**
     * Get instance of autoload (singleton).
     *
     * @return PrestaShopAutoload
     */
    public static function getInstance()
    {
        if (!PrestaShopAutoload::$instance) {
            PrestaShopAutoload::$instance = new PrestaShopAutoload();
        }

        return PrestaShopAutoload::$instance;
    }

    /**
     * Get Class index cache file.
     *
     * @return string
     */
    public static function getCacheFileIndex()
    {
        return _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . (_PS_MODE_DEV_ ? 'dev' : 'prod') . DIRECTORY_SEPARATOR . 'class_index.php';
    }

    /**
     * Get Namespaced class stub file.
     *
     * @return string
     */
    public static function getNamespacedStubFileIndex()
    {
        return _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . (_PS_MODE_DEV_ ? 'dev' : 'prod') . DIRECTORY_SEPARATOR . 'namespaced_class_stub.php';
    }

    /**
     * Get Class stub file.
     *
     * @return string
     */
    public static function getStubFileIndex()
    {
        return _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . (_PS_MODE_DEV_ ? 'dev' : 'prod') . DIRECTORY_SEPARATOR . 'class_stub.php';
    }

    /**
     * Retrieve informations about a class in classes index and load it.
     *
     * @param string $className
     */
    public function load($className)
    {
        // Retrocompatibility
        if (isset(PrestaShopAutoload::$class_aliases[$className]) && !interface_exists($className, false) && !class_exists($className, false)) {
            return eval('class ' . $className . ' extends ' . PrestaShopAutoload::$class_aliases[$className] . ' {}');
        }

        // regenerate the class index if the requested file doesn't exists
        if ((isset($this->index[$className]) && $this->index[$className]['path'] && !is_file($this->root_dir . $this->index[$className]['path']))
            || (isset($this->index[$className . 'Core']) && $this->index[$className . 'Core']['path'] && !is_file($this->root_dir . $this->index[$className . 'Core']['path']))
            || !file_exists(self::getNamespacedStubFileIndex())) {
            $this->generateIndex();
        }

        // If $classname has not core suffix (E.g. Shop, Product)
        if (substr($className, -4) != 'Core' && !class_exists($className, false)) {
            $classDir = (isset($this->index[$className]['override'])
                && $this->index[$className]['override'] === true) ? $this->normalizeDirectory(_PS_ROOT_DIR_) : $this->root_dir;

            // If requested class does not exist, load associated core class
            if (isset($this->index[$className]) && !$this->index[$className]['path']) {
                require_once $classDir . $this->index[$className . 'Core']['path'];

                if ($this->index[$className . 'Core']['type'] != 'interface') {
                    eval($this->index[$className . 'Core']['type'] . ' ' . $className . ' extends ' . $className . 'Core {}');
                }
            } else {
                // request a non Core Class load the associated Core class if exists
                if (isset($this->index[$className . 'Core'])) {
                    require_once $this->root_dir . $this->index[$className . 'Core']['path'];
                }

                if (isset($this->index[$className])) {
                    require_once $classDir . $this->index[$className]['path'];
                }
            }
        } elseif (isset($this->index[$className]['path']) && $this->index[$className]['path']) {
            // Call directly ProductCore, ShopCore class
            require_once $this->root_dir . $this->index[$className]['path'];
        }
        if (strpos($className, 'PrestaShop\PrestaShop\Adapter\Entity') !== false) {
            require_once self::getNamespacedStubFileIndex();
        }
    }

    /**
     * Generate classes index.
     */
    public function generateIndex()
    {
        if (class_exists('Configuration') && defined('_PS_CREATION_DATE_')) {
            $creationDate = _PS_CREATION_DATE_;
            if (!empty($creationDate) && Configuration::get('PS_DISABLE_OVERRIDES')) {
                $this->_include_override_path = false;
            } else {
                $this->_include_override_path = true;
            }
        }

        $coreClasses = $this->getClassesFromDir('classes/');

        $classes = array_merge(
            $coreClasses,
            $this->getClassesFromDir('controllers/')
        );

        $contentNamespacedStub = '<?php ' . "\n" . 'namespace PrestaShop\\PrestaShop\\Adapter\\Entity;' . "\n\n";

        foreach ($coreClasses as $coreClassName => $coreClass) {
            if (substr($coreClassName, -4) == 'Core') {
                $coreClassName = substr($coreClassName, 0, -4);
                if ($coreClass['type'] != 'interface') {
                    $contentNamespacedStub .= $coreClass['type'] . ' ' . $coreClassName . ' extends \\' . $coreClassName . ' {};' . "\n";
                }
            }
        }

        if ($this->_include_override_path) {
            $coreOverrideClasses = $this->getClassesFromDir('override/classes/', defined('_PS_HOST_MODE_'));
            $coreClassesWOOverrides = array_diff_key($coreClasses, $coreOverrideClasses);

            $classes = array_merge(
                $classes,
                $coreOverrideClasses,
                $this->getClassesFromDir('override/controllers/', defined('_PS_HOST_MODE_'))
            );
        } else {
            $coreClassesWOOverrides = $coreClasses;
        }

        $contentStub = '<?php' . "\n\n";

        foreach ($coreClassesWOOverrides as $coreClassName => $coreClass) {
            if (substr($coreClassName, -4) == 'Core') {
                $coreClassNameNoCore = substr($coreClassName, 0, -4);
                if ($coreClass['type'] != 'interface') {
                    $contentStub .= $coreClass['type'] . ' ' . $coreClassNameNoCore . ' extends ' . $coreClassName . ' {};' . "\n";
                }
            }
        }

        ksort($classes);
        $content = '<?php return ' . var_export($classes, true) . '; ?>';

        // Write classes index on disc to cache it
        $filename = PrestaShopAutoload::getCacheFileIndex();
        @mkdir(_PS_CACHE_DIR_, 0777, true);

        if (!$this->dumpFile($filename, $content)) {
            Tools::error_log('Cannot write temporary file ' . $filename);
        }

        $stubFilename = PrestaShopAutoload::getStubFileIndex();
        if (!$this->dumpFile($stubFilename, $contentStub)) {
            Tools::error_log('Cannot write temporary file ' . $stubFilename);
        }

        $namespacedStubFilename = PrestaShopAutoload::getNamespacedStubFileIndex();
        if (!$this->dumpFile($namespacedStubFilename, $contentNamespacedStub)) {
            Tools::error_log('Cannot write temporary file ' . $namespacedStubFilename);
        }

        $this->index = $classes;
    }

    /**
     * @param string $filename
     * @param string $content
     *
     * @return bool
     *
     * @see http://api.symfony.com/3.0/Symfony/Component/Filesystem/Filesystem.html#method_dumpFile
     */
    public function dumpFile($filename, $content)
    {
        $dir = dirname($filename);

        // Will create a temp file with 0600 access rights
        // when the filesystem supports chmod.
        $tmpFile = tempnam($dir, basename($filename));
        if (false === @file_put_contents($tmpFile, $content)) {
            return false;
        }
        // Ignore for filesystems that do not support umask
        @chmod($tmpFile, 0666);
        rename($tmpFile, $filename);

        return true;
    }

    /**
     * Retrieve recursively all classes in a directory and its subdirectories.
     *
     * @param string $path Relative path from root to the directory
     * @param bool $hostMode Since 1.7, deprecated.
     *
     * @return array
     */
    protected function getClassesFromDir($path, $hostMode = false)
    {
        $classes = array();
        $rootDir = $hostMode ? $this->normalizeDirectory(_PS_ROOT_DIR_) : $this->root_dir;

        foreach (scandir($rootDir . $path, SCANDIR_SORT_NONE) as $file) {
            if ($file[0] != '.') {
                if (is_dir($rootDir . $path . $file)) {
                    $classes = array_merge($classes, $this->getClassesFromDir($path . $file . '/', $hostMode));
                } elseif (substr($file, -4) == '.php') {
                    $content = file_get_contents($rootDir . $path . $file);

                    $namespacePattern = '[\\a-z0-9_]*[\\]';
                    $pattern = '#\W((abstract\s+)?class|interface)\s+(?P<classname>' . basename($file, '.php') . '(?:Core)?)'
                                . '(?:\s+extends\s+' . $namespacePattern . '[a-z][a-z0-9_]*)?(?:\s+implements\s+' . $namespacePattern . '[a-z][\\a-z0-9_]*(?:\s*,\s*' . $namespacePattern . '[a-z][\\a-z0-9_]*)*)?\s*\{#i';

                    //DONT LOAD CLASS WITH NAMESPACE - PSR4 autoloaded from composer
                    $usesNamespace = false;
                    foreach (token_get_all($content) as $token) {
                        if ($token[0] === T_NAMESPACE) {
                            $usesNamespace = true;
                            break;
                        }
                    }

                    if (!$usesNamespace && preg_match($pattern, $content, $m)) {
                        $classes[$m['classname']] = array(
                            'path' => $path . $file,
                            'type' => trim($m[1]),
                            'override' => $hostMode,
                        );

                        if (substr($m['classname'], -4) == 'Core') {
                            $classes[substr($m['classname'], 0, -4)] = array(
                                'path' => '',
                                'type' => $classes[$m['classname']]['type'],
                                'override' => $hostMode,
                            );
                        }
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * Get Class path.
     *
     * @param string $classname
     */
    public function getClassPath($classname)
    {
        return (isset($this->index[$classname]) && isset($this->index[$classname]['path'])) ? $this->index[$classname]['path'] : null;
    }

    /**
     * Normalize directory.
     *
     * @param string $directory
     *
     * @return string
     */
    private function normalizeDirectory($directory)
    {
        return rtrim($directory, '/\\') . DIRECTORY_SEPARATOR;
    }
}

spl_autoload_register(array(PrestaShopAutoload::getInstance(), 'load'));
