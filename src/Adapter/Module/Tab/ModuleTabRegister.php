<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
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
    private $defaultParent = 'DEFAULT';
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

    /**
     * @var array List all active languages on the shop
     */
    private $languages;

    public function __construct(TabRepository $tabRepository, LangRepository $langRepository, LoggerInterface $logger, TranslatorInterface $translator, Finder $finder, Filesystem $filesystem, array $languages)
    {
        $this->langRepository = $langRepository;
        $this->tabRepository = $tabRepository;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->finder = $finder;
        $this->filesystem = $filesystem;
        $this->languages = $languages;
    }

    /**
     * Fetch module-defined tabs and find undeclared ModuleAdminControllers.
     *
     * This is done automatically as part of the module installation.
     * @param Module $module
     *
     */
    public function registerTabs(Module $module)
    {
        if (!$module->getInstance()) {
            return;
        }

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

        foreach ($this->getModuleAdminControllersFilename($moduleName) as $adminControllerFileName) {
            $adminControllerName = str_replace('Controller.php', '', $adminControllerFileName);
            if (in_array($adminControllerName, $tabsNames)) {
                continue;
            }

            $tabs[] = array(
                'class_name' => $adminControllerName,
                'visible' => false,
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

    protected function getTabNames($names)
    {
        $translatedNames = array();

        foreach($this->languages as $lang) {
            // In case we just receive a string, we apply it to all languages
            if (!is_array($names)) {
                $translatedNames[$lang['id_lang']] = $names;
            } elseif (array_key_exists($lang['locale'], $names)) {
                $translatedNames[$lang['id_lang']] = $names[$lang['locale']];
            } elseif (array_key_exists($lang['language_code'], $names)) {
                $translatedNames[$lang['id_lang']] = $names[$lang['language_code']];
            } else {
                $translatedNames[$lang['id_lang']] = $names[$lang[0]];
            }
        }
        return $translatedNames;
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
        $tab->active = $data->getBoolean('visible', true);
        $tab->class_name = $data->get('class_name');
        $tab->module = $module->get('name');
        $tab->name = $this->getTabNames($data->get('name', $tab->class_name));
        $tab->icon = $data->get('icon');

        // Handle parent menu
        $parentClassName = $data->get('ParentClassName');
        if (!empty($parentClassName)) {
            $tab->id_parent = (int)$this->tabRepository->findOneIdByClassName($parentClassName);
        } elseif (true === $tab->active) {
            $tab->id_parent = (int)$this->tabRepository->findOneIdByClassName($this->defaultParent);
        } else {
            $tab->id_parent = 0;
        }

        if (!$tab->save()) {
            throw new Exception(
                $this->translator->trans(
                    'Failed to install admin tab "%name%".',
                    array('%name%' => $tab->name),
                    'Admin.Modules.Notification'));
        }
    }
}
