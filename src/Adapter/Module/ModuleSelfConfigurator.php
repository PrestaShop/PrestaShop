<?php
/*
 * 2007-2017 PrestaShop
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
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ModuleSelfConfigurator
{
    protected $module;
    protected $configFile;

    protected $configs = array();

    protected $defaultConfigFile = 'self_config.yml';

    /**
     * @var ModuleRepository
     */
    protected $moduleRepository;
    
    public function __construct(ModuleRepository $moduleRepository)
    {
        $this->module = null;
        $this->configFile = null;

        $this->moduleRepository = $moduleRepository;
    }

    /**
     * Alias for $module setter
     *
     * @param string $name
     * @return $this
     */
    public function module($name)
    {
        return $this->setModule($name);
    }

    /**
     * Set the module to be updated with its name
     *
     * @param string $name
     * @return $this
     * @throws UnexpectedTypeException
     */
    public function setModule($name)
    {
        if (!is_string($name)) {
            throw new UnexpectedTypeException($name, 'string');
        }
        $this->module = $name;
        return $this;
    }

    /**
     * If defined, get the config file path or if possible, guess it.
     * 
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function getFile()
    {
        // If set, return it
        if ($this->configFile) {
            return $this->configFile;
        }

        // If we do not know in which module to search, we cannot go further
        if (!$this->module) {
            return null;
        }

        // Find and store the first config file we find
        $files = Finder::create()
            ->files()
            ->in(_PS_MODULE_DIR_.$this->module)
            ->name($this->defaultConfigFile, null, true);

        foreach ($files as $file) {
            $this->configFile = $file->getRealPath();
            return $this->configFile;
        }
        return null;
    }

    /**
     *  Alias for config file setter
     * 
     * @param string $filepath
     * @return $this
     */
    public function file($filepath)
    {
        return $this->setFile($filepath);
    }

    /**
     * Set the config file to parse
     * 
     * @param string $filepath
     * @return $this
     * @throws UnexpectedTypeException
     */
    public function setFile($filepath)
    {
        if (!is_string($filepath)) {
            throw new UnexpectedTypeException($filepath, 'string');
        }
        
        $this->configFile = $filepath;
        return $this;
    }

    /**
     * In order to prevent some failure, we can check all pre-requesites are respected.
     * Any error will be reported in the array.
     *
     * @return array
     */
    public function validate()
    {
        $errors = array();
        if ($this->module === null) {
            $errors[] = 'Module name not specified';
        }

        try {
            $file = $this->getFile();
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
            $file = null;
        }

        if ($file === null) {
            $errors[] = 'No config file to apply';
        } elseif (!file_exists($file)) {
            $errors[] = 'Specified config file is not found';
        } else {
            try {
                $config = $this->load($file);
            } catch (ParseException $e) {
                $errors[] = $e->getMessage();
            }

            if (empty($config)) {
                $errors = 'Parsed config file is empty';
            }
        }

        if ($this->module && $this->moduleRepository->getModule($this->module)->hasValidInstance()) {
            $errors[] = 'The module specified is invalid';
        }
        
        return $errors;
    }

    /**
     * Launch the self configuration with all the context previously set!
     * 
     * @return boolean
     */
    public function configure()
    {
        return true;
    }

    protected function load($file)
    {
        if (array_key_exists($file, $this->configs)) {
            return $this->configs[$file];
        }
        $this->configs[$file] = Yaml::parse(file_get_contents($file));
        return $this->configs[$file];
    }
}