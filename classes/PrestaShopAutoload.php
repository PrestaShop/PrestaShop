<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\Autoload\LegacyClassLoader;

@trigger_error('Using PrestaShopAutoload is deprecated, use Prestashop\Autoload\PrestaShopAutoload instead', E_USER_DEPRECATED);

/**
 * Class PrestaShopAutoload.
 *
 * @since 1.5
 */
class PrestaShopAutoload
{
    /**
     * @var PrestaShopAutoload|null
     */
    protected static $instance;

    /**
     * @var string Root directory
     */
    protected $root_dir;

    /**
     *  @var array array('classname' => 'path/to/override', 'classnamecore' => 'path/to/class/core')
     */
    public $index = [];

    public $_include_override_path = true;

    /**
     * @var LegacyClassLoader
     */
    protected $classLoader;

    protected static $class_aliases = [
        'Collection' => 'PrestaShopCollection',
        'Autoload' => 'PrestaShopAutoload',
        'Backup' => 'PrestaShopBackup',
        'Logger' => 'PrestaShopLogger',
    ];

    protected function __construct()
    {
        $this->root_dir = _PS_CORE_DIR_ . '/';
        $this->classLoader = new LegacyClassLoader(_PS_ROOT_DIR_, _PS_CACHE_DIR_);
        $file = $this->classLoader->getClassIndexFilepath();
        $stubFile = static::getStubFileIndex();
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
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Get Class index cache file.
     *
     * @return string
     */
    public static function getCacheFileIndex()
    {
        return _PS_CACHE_DIR_ . 'class_index.php';
    }

    /**
     * Get Namespaced class stub file.
     *
     * @return string
     */
    public static function getNamespacedStubFileIndex()
    {
        return _PS_CACHE_DIR_ . 'namespaced_class_stub.php';
    }

    /**
     * Get Class stub file.
     *
     * @return string
     */
    public static function getStubFileIndex()
    {
        return _PS_CACHE_DIR_ . 'class_stub.php';
    }

    /**
     * Retrieve informations about a class in classes index and load it.
     *
     * @param string $className
     */
    public function load($className)
    {
        // Retrocompatibility
        if (isset(static::$class_aliases[$className]) && !interface_exists($className, false) && !class_exists($className, false)) {
            return eval('class ' . $className . ' extends ' . static::$class_aliases[$className] . ' {}');
        }

        // regenerate the class index if the requested file doesn't exists
        if ((isset($this->index[$className]) && $this->index[$className]['path'] && !is_file($this->root_dir . $this->index[$className]['path']))
            || (isset($this->index[$className . 'Core']) && $this->index[$className . 'Core']['path'] && !is_file($this->root_dir . $this->index[$className . 'Core']['path']))
            || !file_exists(static::getNamespacedStubFileIndex())) {
            $this->generateIndex();
        }

        // If $classname has not core suffix (E.g. Shop, Product)
        if (substr($className, -4) != 'Core' && !class_exists($className, false)) {
            // If requested class does not exist, load associated core class
            if (isset($this->index[$className]) && !$this->index[$className]['path']) {
                require_once $this->root_dir . $this->index[$className . 'Core']['path'];

                if ($this->index[$className . 'Core']['type'] != 'interface') {
                    eval($this->index[$className . 'Core']['type'] . ' ' . $className . ' extends ' . $className . 'Core {}');
                }
            } else {
                // request a non Core Class load the associated Core class if exists
                if (isset($this->index[$className . 'Core'])) {
                    require_once $this->root_dir . $this->index[$className . 'Core']['path'];
                }

                if (isset($this->index[$className])) {
                    require_once $this->root_dir . $this->index[$className]['path'];
                }
            }
        } elseif (isset($this->index[$className]['path']) && $this->index[$className]['path']) {
            // Call directly ProductCore, ShopCore class
            require_once $this->root_dir . $this->index[$className]['path'];
        }
        if (strpos($className, 'PrestaShop\PrestaShop\Adapter\Entity') !== false) {
            $legacyClass = substr($className, 37);
            $this->load($legacyClass);
            class_alias($legacyClass, '\\' . $className);
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

        $this->index = $this->classLoader->buildClassIndex($this->_include_override_path);
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
        @chmod($tmpFile, file_exists($filename) ? fileperms($filename) : 0666 & ~umask());
        rename($tmpFile, $filename);

        return true;
    }

    /**
     * Retrieve recursively all classes in a directory and its subdirectories.
     *
     * @param string $path Relative path from root to the directory
     *
     * @return array
     */
    protected function getClassesFromDir($path)
    {
        $rootDir = $this->root_dir;
        if (!is_dir($rootDir . $path)) {
            return [];
        }

        $classes = [];
        foreach (scandir($rootDir . $path, SCANDIR_SORT_NONE) as $file) {
            if ($file[0] != '.') {
                if (is_dir($rootDir . $path . $file)) {
                    $classes = array_merge($classes, $this->getClassesFromDir($path . $file . '/'));
                } elseif (substr($file, -4) == '.php') {
                    $content = file_get_contents($rootDir . $path . $file);

                    $namePattern = '[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*';
                    $nameWithNsPattern = '(?:\\\\?(?:' . $namePattern . '\\\\)*' . $namePattern . ')';
                    $pattern = '~(?<!\w)((abstract\s+)?class|interface)\s+(?P<classname>' . basename($file, '.php') . '(?:Core)?)'
                                . '(?:\s+extends\s+' . $nameWithNsPattern . ')?(?:\s+implements\s+' . $nameWithNsPattern . '(?:\s*,\s*' . $nameWithNsPattern . ')*)?\s*\{~i';

                    //DONT LOAD CLASS WITH NAMESPACE - PSR4 autoloaded from composer
                    $usesNamespace = false;
                    foreach (token_get_all($content) as $token) {
                        if ($token[0] === T_NAMESPACE) {
                            $usesNamespace = true;

                            break;
                        }
                    }

                    if (!$usesNamespace && preg_match($pattern, $content, $m)) {
                        $classes[$m['classname']] = [
                            'path' => $path . $file,
                            'type' => trim($m[1]),
                        ];

                        if (substr($m['classname'], -4) == 'Core') {
                            $classes[substr($m['classname'], 0, -4)] = [
                                'path' => '',
                                'type' => $classes[$m['classname']]['type'],
                            ];
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
        return (isset($this->index[$classname]['path'])) ? $this->index[$classname]['path'] : null;
    }
}
