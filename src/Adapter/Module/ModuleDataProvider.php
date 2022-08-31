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

namespace PrestaShop\PrestaShop\Adapter\Module;

use Db;
use Doctrine\ORM\EntityManager;
use Module as LegacyModule;
use PhpParser;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Addon\Module\AddonListFilterDeviceStatus;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * This class will provide data from DB / ORM about Module.
 */
class ModuleDataProvider
{
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * EntityManager for module history.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var int
     */
    private $employeeID;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, EntityManager $entityManager = null)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->employeeID = 0;
    }

    /**
     * @param int $employeeID
     */
    public function setEmployeeId($employeeID)
    {
        $this->employeeID = (int) $employeeID;
    }

    /**
     * Return all module information from database.
     *
     * @param string $name The technical module name to search
     *
     * @return array
     */
    public function findByName($name)
    {
        $result = Db::getInstance()->getRow(
            sprintf(
                'SELECT `id_module` as `id`, `active`, `version` FROM `%smodule` WHERE `name` = "%s"',
                _DB_PREFIX_,
                pSQL($name)
            )
        );
        /** @var array{id: string, active: string, version: string}|false|null $result */
        if ($result) {
            $result['installed'] = 1;
            $result['active'] = $this->isEnabled($name);
            $result['active_on_mobile'] = (bool) ($this->getDeviceStatus($name) & AddonListFilterDeviceStatus::DEVICE_MOBILE);
            $lastAccessDate = '0000-00-00 00:00:00';

            if (!Tools::isPHPCLI() && null !== $this->entityManager && $this->employeeID) {
                $moduleID = (int) $result['id'];

                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('mh')
                    ->from('PrestaShopBundle:ModuleHistory', 'mh', 'mh.idModule')
                    ->where('mh.idEmployee = ?1')
                    ->setParameter(1, $this->employeeID);
                $query = $qb->getQuery();
                $query->useResultCache(true);
                $modulesHistory = $query->getResult();

                if (array_key_exists($moduleID, $modulesHistory)) {
                    $lastAccessDate = $modulesHistory[$moduleID]->getDateUpd()->format('Y-m-d H:i:s');
                }
            }
            $result['last_access_date'] = $lastAccessDate;

            return $result;
        }

        return [
            'installed' => 0,
        ];
    }

    /**
     * Return installed modules along with their id, name and version
     * If a specific shop is selected, active and active_on_mobile keys are added
     *
     * @return array
     */
    public function getInstalled(): array
    {
        $select = 'SELECT m.`id_module` as id, m.`name`, m.`version`, 1 as installed';
        $from = ' FROM `' . _DB_PREFIX_ . 'module` m';

        $id_shops = (new Context())->getContextListShopID();
        if (count($id_shops) === 1) {
            $select .= ', ms.`id_module` as active, ms.`enable_device` as active_on_mobile';
            $from .= ' LEFT JOIN `' . _DB_PREFIX_ . 'module_shop` ms ON ms.`id_module` = m.`id_module`';
            $from .= ' AND ms.`id_shop` = ' . reset($id_shops);
        }

        $results = Db::getInstance()->executeS($select . $from);
        $modules = [];

        /** @var array{id: int, name:string, version: string, installed: int}|array{id: int, name:string, version: string, installed: int, active:int, active_on_mobile: int} $module */
        foreach ($results as $module) {
            $module['installed'] = (bool) $module['installed'];
            if (array_key_exists('active_on_mobile', $module)) {
                $module['active_on_mobile'] = (bool) ($module['active_on_mobile'] & AddonListFilterDeviceStatus::DEVICE_MOBILE);
            }
            if (array_key_exists('active', $module)) {
                $module['active'] = (bool) $module['active'];
            }
            $modules[$module['name']] = $module;
        }

        return $modules;
    }

    /**
     * Return translated module *Display Name*.
     *
     * @param string $module The technical module name
     *
     * @return string The translated Module displayName
     */
    public function getModuleName($module)
    {
        return LegacyModule::getModuleName($module);
    }

    /**
     * Check current employee permission on a given module.
     *
     * @param string $action
     * @param string $name
     *
     * @return bool True if allowed
     */
    public function can($action, $name)
    {
        $module_id = LegacyModule::getModuleIdByName($name);

        if (empty($module_id)) {
            return false;
        }

        return LegacyModule::getPermissionStatic($module_id, $action);
    }

    /**
     * Check if a module is enabled in the current shop context.
     *
     * @param string $name The technical module name
     *
     * @return bool True if enable
     */
    public function isEnabled($name)
    {
        $id_shops = (new Context())->getContextListShopID();
        // ToDo: Load list of all installed modules ?

        $result = Db::getInstance()->getRow('SELECT m.`id_module` as `active`, ms.`id_module` as `shop_active`
        FROM `' . _DB_PREFIX_ . 'module` m
        LEFT JOIN `' . _DB_PREFIX_ . 'module_shop` ms ON m.`id_module` = ms.`id_module`
        WHERE `name` = "' . pSQL($name) . '"
        AND ms.`id_shop` IN (' . implode(',', array_map('intval', $id_shops)) . ')');
        if ($result) {
            return (bool) ($result['active'] && $result['shop_active']);
        } else {
            return false;
        }
    }

    public function isInstalled($name)
    {
        // ToDo: Load list of all installed modules ?
        return (bool) $this->getModuleIdByName($name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isInstalledAndActive(string $name): bool
    {
        return (bool) $this->getModuleIdByName($name, true);
    }

    /**
     * Returns the Module Id
     *
     * @param string $name The technical module name
     * @param bool $activeModulesOnly Should we return the module only if it's active ?
     *
     * @return int the Module Id, or 0 if not found
     */
    public function getModuleIdByName($name, bool $activeModulesOnly = false)
    {
        $sqlQuery = 'SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module` WHERE `name` = "' . pSQL($name) . '"';
        if ($activeModulesOnly) {
            $sqlQuery .= ' AND `active` = 1';
        }

        return (int) Db::getInstance()->getValue(
            $sqlQuery
        );
    }

    /**
     * We won't load an invalid class. This function will check any potential parse error.
     *
     * @param string $name The technical module name to check
     *
     * @return bool true if valid
     */
    public function isModuleMainClassValid($name)
    {
        if (!Validate::isModuleName($name)) {
            return false;
        }

        $file_path = _PS_MODULE_DIR_ . $name . '/' . $name . '.php';
        // Check if file exists (slightly faster than file_exists)
        if (!(int) @filemtime($file_path)) {
            return false;
        }

        $parser = (new PhpParser\ParserFactory())->create(PhpParser\ParserFactory::ONLY_PHP7);
        $log_context_data = [
            'object_type' => 'Module',
            'object_id' => LegacyModule::getModuleIdByName($name),
        ];

        try {
            $parser->parse(file_get_contents($file_path));
        } catch (PhpParser\Error $exception) {
            $this->logger->critical(
                $this->translator->trans(
                    'Parse error detected in main class of module %module%: %parse_error%',
                    [
                        '%module%' => $name,
                        '%parse_error%' => $exception->getMessage(),
                    ],
                    'Admin.Modules.Notification'
                ),
                $log_context_data
            );

            return false;
        }

        $logger = $this->logger;
        // -> Even if we do not detect any parse error in the file, we may have issues
        // when trying to load the file. (i.e with additional require_once).
        // -> We use an anonymous function here because if a test is made twice
        // on the same module, the test on require_once would immediately return true
        // (as the file would have already been evaluated).
        $require_correct = function ($name) use ($file_path, $logger, $log_context_data) {
            try {
                require_once $file_path;
            } catch (\Exception $e) {
                $logger->error(
                    $this->translator->trans(
                        'Error while loading file of module %module%. %error_message%',
                        [
                            '%module%' => $name,
                            '%error_message%' => $e->getMessage(), ],
                        'Admin.Modules.Notification'
                    ),
                    $log_context_data
                );

                return false;
            }

            return true;
        };

        return $require_correct($name);
    }

    /**
     * Check if the module is in the modules folder, with a valid class.
     *
     * @param string $name The technical module name to find
     *
     * @return bool True if found
     */
    public function isOnDisk($name)
    {
        $path = _PS_MODULE_DIR_ . $name . '/' . $name . '.php';

        return file_exists($path);
    }

    /**
     * Check if the module has been enabled on mobile.
     *
     * @param string $name The technical module name to check
     *
     * @return int|false The devices enabled for this module
     */
    private function getDeviceStatus($name)
    {
        $id_shops = (new Context())->getContextListShopID();
        // ToDo: Load list of all installed modules ?

        $result = Db::getInstance()->getRow('SELECT m.`id_module` as `active`, ms.`id_module` as `shop_active`, ms.`enable_device` as `enable_device`
            FROM `' . _DB_PREFIX_ . 'module` m
            LEFT JOIN `' . _DB_PREFIX_ . 'module_shop` ms ON m.`id_module` = ms.`id_module`
            WHERE `name` = "' . pSQL($name) . '"
            AND ms.`id_shop` IN (' . implode(',', array_map('intval', $id_shops)) . ')');
        if ($result) {
            return (int) $result['enable_device'];
        }

        return false;
    }
}
