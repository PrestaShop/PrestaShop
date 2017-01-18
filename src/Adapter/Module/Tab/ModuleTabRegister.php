<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module\Tab;

use Exception;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Translation\TranslatorInterface;
use TabCore as Tab;

class ModuleTabRegister
{
    /**
     * @var LangRepository
     */
    protected $langRepository;

    /**
     * @var TabRepository
     */
    protected $tabRepository;

    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;
    
    /**
     * @var Finder
     */
    private $finder;
    
    /**
     * @var Filesystem
     */
    private $filesystem;
    
    public function __construct(TabRepository $tabRepository, LangRepository $langRepository, LoggerInterface $logger, TranslatorInterface $translator, Finder $finder, Filesystem $filesystem)
    {
        $this->langRepository = $langRepository;
        $this->tabRepository = $tabRepository;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->finder = $finder;
        $this->filesystem = $filesystem;
    }
    
    /**
     * Install all module-defined tabs.
     *
     * This is done automatically as part of the module installation.
     * @param Module $module
     * 
     */
    public function registerTabs(Module $module)
    {
        $tabs = $this->addUndeclaredTabs($module->get('name'), $module->getInstance()->getTabs());
        
        foreach ($tabs as $tab) {
            try {
                $this->registerTab($module, new ParameterBag($tab));
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

    }
    
    /**
     * Looks for ModuleAdminControllers not declared as Tab and 
     * add them to the list to register
     * 
     * @param string $moduleName
     * @param array $tabs
     * @return array
     */
    protected function addUndeclaredTabs($moduleName, array $tabs)
    {
        // Function to get only class name from tabs already declared
        $tabsNames = array_map(function($tab) {
            if (array_key_exists('class_name', $tab)) {
                return $tab['class_name'];
            }
        }, $tabs);
        
        foreach ($this->getModuleAdminControllersFilename($moduleName) as $adminControllerName) {
            if (in_array($adminControllerName, $tabsNames)) {
                continue;
            }
            
            $tabs[] = array(
                'class_name' => str_replace('Controller.php', '', $adminControllerName),
            );
        }
        
        return $tabs;
    }
    
    /**
     * Check mandatory data for tab registration, such as class name and class exists
     * 
     * @param string $moduleName
     * @param ParameterBag $data
     * @return boolean (= true) when no issue detected
     * @throws Exception in case of invalid data
     */
    protected function checkIsValid($moduleName, ParameterBag $data)
    {
        $className = $data->get('class_name', null);
        if (null === $className) {
            throw new Exception('Missing class name of tab');
        }
        // Check controller exists
        if (!in_array($className.'Controller.php', $this->getModuleAdminControllersFilename($moduleName))) {
            throw new Exception(sprintf('Class "%sController" not found in controllers/admin', $className));
        }
        return true;
    }
    
    /**
     * Find all ModuleAdminController classes from module.
     * This allow to check a class exists for a registered tab and to register automatically all the classes
     * not explicitely declared by the module developer.
     * 
     * @param string $moduleName
     * @return array of Symfony\Component\Finder\SplFileInfo, listing all the ModuleAdminControllers found
     */
    protected function getModuleAdminControllers($moduleName)
    {
        $modulePath = _PS_ROOT_DIR_.'/'.basename(_PS_MODULE_DIR_).
                '/'.$moduleName.'/controllers/admin/';
        
        if (!$this->filesystem->exists($modulePath)) {
            return array();
        }
        
        $moduleFolder = $this->finder->files()
                    ->in($modulePath)
                    ->depth('== 0')
                    ->name('*.php')
                    ->exclude(['index.php'])
                    ->contains('/extends\s+ModuleAdminController/i');
        
        return iterator_to_array($moduleFolder);
    }
    
    /**
     * Convert SPLFileInfo array to file names. Better & easier to check if a class to register exists.
     * 
     * @param string $moduleName
     * @return array of strings
     */
    protected function getModuleAdminControllersFilename($moduleName)
    {
        return array_map(function(SplFileInfo $file) {
            return $file->getFilename();
        }, $this->getModuleAdminControllers($moduleName));
    }
    
    /**
     * Install a tab according to its defined structure
     *
     * @param Module $module
     * @param ParameterBag $data The structure of the tab.
     *
     * @throws Exception in case of error from validation or save
     */
    protected function registerTab(Module $module, ParameterBag $data)
    {
        $this->checkIsValid($module->get('name'), $data);
        
        // Legacy Tab, to be replaced with Doctrine entity when right management
        // won't be directly linked to the tab creation
        // @ToDo
        $tab = new Tab();
        $tab->active = $data->getBoolean('active', false);
        $tab->class_name = $data->get('class_name');
        $tab->module = $module->get('name');
        $tab->name = $data->get('name', $tab->class_name);

        // Handle hidden or root position
        $parentClassName = $data->get('ParentClassName', null);
        if (true === $data->getBoolean('hidden', false)) {
            $tab->id_parent = -1;
        } elseif (!empty($parentClassName)) {
            $tab->id_parent = $this->tabRepository->findOneByClassName($parentClassName);
        } else {
            $tab->id_parent = 0;
        }
        
        if (!$tab->save()) {
            throw new Exception(
                $this->translator->trans(
                    'Failed to install admin tab "%name%".',
                    array(
                        '%name%' => $tab->name,
                    ),
                    'Admin.Modules.Notification'));
        }
    }
}
