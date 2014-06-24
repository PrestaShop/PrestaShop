<?php
/**
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

class ModuleOverrideCore
{
  /**
   * The current module name who loaded
   * @var string
   */
  protected $module_name;

  /**
   * Path of module core (in modules/ dir)
   * @var string
   */
  protected $module_core_path;

  /**
   * Filemtime of all overrided module
   * @var array
   */
  protected $overrided_module;

  /**
   * Constructor
   * Init the constant and property
   * @param string $moduleName
   */
  protected function __construct($moduleName)
  {
    $this->module_name = $moduleName;

    if(!defined('_PS_THEME_CACHE_DIR_'))
      define('_PS_THEME_CACHE_DIR_', _PS_THEME_DIR_.'cache'.DS.'modules'.DS);

    if(!is_dir(_PS_THEME_CACHE_DIR_))
      mkdir(_PS_THEME_CACHE_DIR_, 0705);

    $this->module_core_path = _PS_THEME_CACHE_DIR_.$this->module_name.'.core.php';

    if(file_exists(_PS_THEME_CACHE_DIR_.'module_index.php'))
      $this->overrided_module = include _PS_THEME_CACHE_DIR_.'module_index.php';
    
    if(!is_array($this->overrided_module))
      $this->overrided_module = array();
  }

  /**
   * Load the right classes
   * @param string $moduleName
   * @static 
   */
  public static function load($moduleName)
  {
    $self = new self($moduleName);
    $self->_load();
  }

  /**
   * Load all classes for this module
   */
  protected function _load()
  {
    // If not override, then we load basic file
    if(!file_exists(_PS_THEME_DIR_.'modules/'.$this->module_name.'/'.$this->module_name.'.php'))
    {
      if(file_exists(_PS_MODULE_DIR_.$this->module_name.'/'.$this->module_name.'.php'))
      {
        include_once _PS_MODULE_DIR_.$this->module_name.'/'.$this->module_name.'.php';
      }
    }
    else
    {
      // else we load the parent class
      $this->loadOverridedModule();
      // and the child class
      require_once _PS_THEME_DIR_.'modules/'.$this->module_name.'/'.$this->module_name.'.php';
    }
  }


  /**
   * Load and generate the parent classe
   */
  protected function loadOverridedModule()
  {
    if(!file_exists($this->module_core_path) || $this->hasChanged())
      $this->generateCodeModuleFile();
    require_once $this->module_core_path;
  }

  /**
   * Generate the parent class (with change name)
   * and update the filemtime file
   */
  protected function generateCodeModuleFile()
  {
    // Rewrite the name class
    $moduleCore = preg_replace('/class\s+([a-zA-Z0-9_-]+)/', 'class $1Module', file_get_contents(_PS_MODULE_DIR_.$this->module_name.'/'.$this->module_name.'.php'));
    // Rewrite the dirname rules
    $moduleCore = preg_replace('/dirname\(__FILE__\)/i', '\''._PS_MODULE_DIR_.$this->module_name.'\'', $moduleCore);
    // Replace the private methods by protected (for allowed rewrite in extended classes)
    $moduleCore = str_ireplace('private', 'protected', $moduleCore);
    
    file_put_contents($this->module_core_path, $moduleCore, LOCK_EX);
    $this->overrided_module[$this->module_name] = filemtime(_PS_MODULE_DIR_.$this->module_name.'/'.$this->module_name.'.php');
    $this->generateIndex();
  }

  /**
   * Return true if the file of parent class was a change
   * @return bool
   */
  protected function hasChanged()
  {
    return !array_key_exists($this->module_name, $this->overrided_module) || $this->overrided_module[$this->module_name] != filemtime(_PS_MODULE_DIR_.$this->module_name.'/'.$this->module_name.'.php');
  }

  /**
   * Generate file width array of filetime
   */
  protected function generateIndex()
  {
    $content = '<?php return '.var_export($this->overrided_module, true).'; ?>';

    // Write classes index on disc to cache it
    $filename = _PS_THEME_CACHE_DIR_.'module_index.php';
    if ((file_exists($filename) && !is_writable($filename)) || !is_writable(dirname($filename)))
    {
      header('HTTP/1.1 503 temporarily overloaded');
      // Cannot use PrestaShopException in this context
      die($filename.' is not writable, please give write permissions (chmod 666) on this file.');
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
  }
}
