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

namespace PrestaShop\PrestaShop\Adapter\Module\Tab;

use Exception;
use PrestaShop\PrestaShop\Core\Module\ModuleInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Translation\TranslatorInterface;
use TabCore as Tab;

/**
 * Class responsible of register new tab in Back Office's menu.
 */
class ModuleTabRegister
{
    public const SUFFIX = '_MTR';

    /**
     * @var string
     */
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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array List all active languages on the shop
     */
    private $languages;

    /**
     * @var Loader
     */
    private $routingConfigLoader;

    /**
     * @param TabRepository $tabRepository
     * @param LangRepository $langRepository
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param Filesystem $filesystem
     * @param array $languages
     * @param Loader $routingConfigLoader
     */
    public function __construct(
        TabRepository $tabRepository,
        LangRepository $langRepository,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        Filesystem $filesystem,
        array $languages,
        Loader $routingConfigLoader
    ) {
        $this->langRepository = $langRepository;
        $this->tabRepository = $tabRepository;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->filesystem = $filesystem;
        $this->languages = $languages;
        $this->routingConfigLoader = $routingConfigLoader;
    }

    /**
     * Fetch module-defined tabs and find undeclared ModuleAdminControllers.
     *
     * This is done automatically as part of the module installation.
     *
     * @param ModuleInterface $module
     */
    public function registerTabs(ModuleInterface $module)
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
     * @param ModuleInterface $module
     */
    public function enableTabs(ModuleInterface $module)
    {
        $this->tabRepository->changeEnabledByModuleName($module->get('name'), true);
    }

    /**
     * Looks for ModuleAdminControllers not declared as Tab and
     * add them to the list to register.
     *
     * @param string $moduleName
     * @param array $tabs
     *
     * @return array
     */
    protected function addUndeclaredTabs($moduleName, array $tabs)
    {
        // Function to get only class name from tabs already declared
        $tabsNames = array_map(function ($tab) {
            if (array_key_exists('class_name', $tab)) {
                return $tab['class_name'];
            }
        }, $tabs);

        $detectedControllers = $this->getDetectedModuleControllers($moduleName);
        foreach ($detectedControllers as $adminControllerName) {
            if (in_array($adminControllerName, $tabsNames)) {
                continue;
            }

            if ($this->tabRepository->findOneIdByClassName($adminControllerName)) {
                continue;
            }

            $tabs[] = [
                'class_name' => $adminControllerName,
                'visible' => false,
            ];
        }

        return $tabs;
    }

    /**
     * Returns a list of all detected controllers, either from admin/controllers folder
     * or from the routing file.
     *
     * @param string $moduleName
     *
     * @return array
     *
     * @throws Exception
     */
    protected function getDetectedModuleControllers(string $moduleName): array
    {
        $legacyControllersFilenames = $this->getModuleAdminControllersFilename($moduleName);
        $legacyControllers = array_map(function ($legacyControllersFilename) {
            return str_replace('Controller.php', '', $legacyControllersFilename);
        }, $legacyControllersFilenames);

        $routingControllers = $this->getModuleControllersFromRouting($moduleName);

        return array_merge($legacyControllers, $routingControllers);
    }

    /**
     * Check mandatory data for tab registration, such as class name and class exists.
     *
     * @param string $moduleName
     * @param ParameterBag $data
     *
     * @return bool (= true) when no issue detected
     *
     * @throws Exception in case of invalid data
     */
    protected function checkIsValid($moduleName, ParameterBag $data)
    {
        $className = $data->get('class_name', null);
        if (null === $className) {
            throw new Exception('Missing class name of tab');
        }

        // Check controller exists
        $detectedControllers = $this->getDetectedModuleControllers($moduleName);
        if (empty($data->get('route_name')) && !in_array($className, $detectedControllers)) {
            throw new Exception(sprintf('Class "%sController" not found in controllers/admin nor routing file', $className));
        }

        // Deprecation check
        if ($data->has('ParentClassName') && !$data->has('parent_class_name')) {
            $this->logger->warning('Tab attribute "ParentClassName" is deprecated. You must use "parent_class_name" instead.');
        }
        //Check if the tab was already added manually
        if (!empty($this->tabRepository->findOneIdByClassName($className))) {
            throw new Exception(sprintf('Cannot register tab "%s" because it already exists', $className));
        }

        return true;
    }

    /**
     * Find all ModuleAdminController classes from module.
     * This allow to check a class exists for a registered tab and to register automatically all the classes
     * not explicitely declared by the module developer.
     *
     * @param string $moduleName
     *
     * @return array of Symfony\Component\Finder\SplFileInfo, listing all the ModuleAdminControllers found
     */
    protected function getModuleAdminControllers($moduleName)
    {
        $modulePath = rtrim(_PS_MODULE_DIR_, '/') . '/' . $moduleName . '/controllers/admin/';

        if (!$this->filesystem->exists($modulePath)) {
            return [];
        }

        $moduleFolder = Finder::create()->files()
            ->in($modulePath)
            ->depth('== 0')
            ->name('*Controller.php')
            ->exclude(['index.php'])
            ->contains('/Controller\s+extends\s+/i');

        return iterator_to_array($moduleFolder);
    }

    /**
     * Parses the routes file from the module and return the list of associated controller
     * via the _legacy_controller routing option.
     *
     * @param string $moduleName
     *
     * @return string[]
     *
     * @throws Exception
     */
    protected function getModuleControllersFromRouting(string $moduleName): array
    {
        $routingFile = rtrim(_PS_MODULE_DIR_, '/') . '/' . $moduleName . '/config/routes.yml';
        if (!$this->filesystem->exists($routingFile)) {
            return [];
        }

        $routingControllers = [];
        $moduleRoutes = $this->routingConfigLoader->import($routingFile, 'yaml');
        foreach ($moduleRoutes->getIterator() as $route) {
            $legacyController = $route->getDefault('_legacy_controller');
            if (!empty($legacyController)) {
                $routingControllers[] = $legacyController;
            }
        }

        return $routingControllers;
    }

    /**
     * Convert SPLFileInfo array to file names. Better & easier to check if a class to register exists.
     *
     * @param string $moduleName
     *
     * @return array of strings
     */
    protected function getModuleAdminControllersFilename($moduleName)
    {
        return array_map(function (SplFileInfo $file) {
            return $file->getFilename();
        }, $this->getModuleAdminControllers($moduleName));
    }

    /**
     * From the name given by the module maintainer, associate a value per language
     * installed on the shop.
     *
     * @param mixed $names
     *
     * @return array Name to use for each installed language
     */
    protected function getTabNames($names)
    {
        $translatedNames = [];

        foreach ($this->languages as $lang) {
            // In case we just receive a string, we apply it to all languages
            if (!is_array($names)) {
                $translatedNames[$lang['id_lang']] = $names;
            } elseif (array_key_exists($lang['locale'], $names)) {
                $translatedNames[$lang['id_lang']] = $names[$lang['locale']];
            } elseif (array_key_exists($lang['language_code'], $names)) {
                $translatedNames[$lang['id_lang']] = $names[$lang['language_code']];
            } elseif (array_key_exists($lang['iso_code'], $names)) {
                $translatedNames[$lang['id_lang']] = $names[$lang['iso_code']];
            } else {
                $translatedNames[$lang['id_lang']] = reset($names); // Get the first name available in the array
            }
        }

        return $translatedNames;
    }

    /**
     * Install a tab according to its defined structure.
     *
     * @param ModuleInterface $module
     * @param ParameterBag $tabDetails the structure of the tab
     *
     * @throws Exception in case of error from validation or save
     */
    protected function registerTab(ModuleInterface $module, ParameterBag $tabDetails)
    {
        $this->checkIsValid($module->get('name'), $tabDetails);

        /**
         * Legacy Tab, to be replaced with Doctrine entity when right management
         * won't be directly linked to the tab creation
         *
         * @ToDo
         */
        $tab = new Tab();
        $tab->active = $tabDetails->getBoolean('visible', true);
        $tab->enabled = true;
        $tab->class_name = $tabDetails->get('class_name');
        $tab->route_name = $tabDetails->get('route_name');
        $tab->module = $module->get('name');
        $tab->name = $this->getTabNames($tabDetails->get('name', $tab->class_name));
        $tab->icon = $tabDetails->get('icon');
        $tab->id_parent = $this->findParentId($tabDetails);
        $tab->wording = $tabDetails->get('wording');
        $tab->wording_domain = $tabDetails->get('wording_domain');

        if (!$tab->save()) {
            throw new Exception($this->translator->trans('Failed to install admin tab "%name%".', ['%name%' => $tab->name], 'Admin.Modules.Notification'));
        }
    }

    /**
     * Find the parent ID from the given tab context.
     *
     * @param ParameterBag $tabDetails the structure of the tab
     *
     * @return int ID of the parent, 0 if none
     */
    protected function findParentId(ParameterBag $tabDetails)
    {
        $idParent = 0;
        $parentClassName = $tabDetails->get('parent_class_name', $tabDetails->get('ParentClassName'));
        if (!empty($parentClassName)) {
            // Could be a previously duplicated tab
            $idParent = $this->tabRepository->findOneIdByClassName($parentClassName . self::SUFFIX);
            if (!$idParent) {
                $idParent = $this->tabRepository->findOneIdByClassName($parentClassName);
            }
        } elseif (true === $tabDetails->getBoolean('visible', true)) {
            $idParent = $this->tabRepository->findOneIdByClassName($this->defaultParent);
        }

        return $this->duplicateParentIfAlone((int) $idParent);
    }

    /**
     * When the tab you add is the first child of a parent tab, we must duplicate it in the children
     * or its link will be overriden.
     *
     * @param int $idParent
     *
     * @return int new parent ID
     */
    protected function duplicateParentIfAlone($idParent)
    {
        // If the given parent has already children, don't touch anything
        if ($idParent === 0 || count($this->tabRepository->findByParentId($idParent))) {
            return $idParent;
        }

        $currentTab = new Tab($idParent);
        $newTab = clone $currentTab;
        $newTab->id = 0;
        $newTab->id_parent = $currentTab->id_parent;
        $newTab->class_name = $currentTab->class_name . self::SUFFIX;
        $newTab->save();

        // Second save in order to get the proper position (add() resets it)
        $newTab->position = $currentTab->position;
        $newTab->save();

        $currentTab->id_parent = $newTab->id;
        $currentTab->save();

        return $newTab->id;
    }
}
