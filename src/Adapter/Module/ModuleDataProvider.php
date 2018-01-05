<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Module;

use Doctrine\ORM\EntityManager;
use PhpParser;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Addon\Module\AddonListFilterDeviceStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Db;
use Validate;
use Module as LegacyModule;

class ModuleDataProvider
{
    /**
     * Logger
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Translator
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * EntityManager for module history
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var integer
     */
    private $employeeID;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, EntityManager $entityManager = null)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->employeeID = 0;
    }

    public function setEmployeeId($employeeID)
    {
        $this->employeeID = (int)$employeeID;
    }

    /**
     * Return all module information from database
     * @param string $name The technical module name to search
     * @return array
     */
    public function findByName($name)
    {
        $result = Db::getInstance()->getRow('SELECT `id_module` as `id`, `active`, `version` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.pSQL($name).'"');
        if ($result) {
            $result['installed'] = 1;
            $result['active'] = $this->isEnabled($name);
            $result['active_on_mobile'] = (bool)($this->getDeviceStatus($name) & AddonListFilterDeviceStatus::DEVICE_MOBILE);
            $lastAccessDate = '0000-00-00 00:00:00';

            if (!Tools::isPHPCLI() && !is_null($this->entityManager) && $this->employeeID) {
                $moduleID = (int)$result['id'];

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

        return ['installed' => 0];
    }


    /**
     * Return translated module *Display Name*
     * @param string $module The technical module name
     * @return string The translated Module displayName
     */
    public function getModuleName($module)
    {
        return LegacyModule::getModuleName($module);
    }

    /**
     * Check current employee permission on a given module
     * @param string $action
     * @param string $name
     * @return bool True if allowed
     */
    public function can($action, $name)
    {
        return LegacyModule::getPermissionStatic(
            LegacyModule::getModuleIdByName($name),
            $action
        );
    }

    /**
     * Check if a module is enabled in the current shop context
     * @param boolean $name The technical module name
     * @return boolean True if enable
     */
    public function isEnabled($name)
    {
        $id_shops = (new Context())->getContextListShopID();
        // ToDo: Load list of all installed modules ?

        $result = Db::getInstance()->getRow('SELECT m.`id_module` as `active`, ms.`id_module` as `shop_active`
        FROM `'._DB_PREFIX_.'module` m
        LEFT JOIN `'._DB_PREFIX_.'module_shop` ms ON m.`id_module` = ms.`id_module`
        WHERE `name` = "'. pSQL($name) .'"
        AND ms.`id_shop` IN ('.implode(',', array_map('intval', $id_shops)).')');
        if ($result) {
            return (bool)($result['active'] && $result['shop_active']);
        } else {
            return false;
        }
    }


    public function isInstalled($name)
    {
        // ToDo: Load list of all installed modules ?
        return (bool)Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.pSQL($name).'"');
    }


    /**
     * We won't load an invalid class. This function will check any potential parse error
     *
     * @param  string $name The technical module name to check
     * @return bool true if valid
     */
    public function isModuleMainClassValid($name)
    {
        if (!Validate::isModuleName($name)) {
            return false;
        }

        $file_path = _PS_MODULE_DIR_.$name.'/'.$name.'.php';
        // Check if file exists (slightly faster than file_exists)
        if (!(int)@filemtime($file_path)) {
            return false;
        }

        $parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7);
        try {
            $parser->parse(file_get_contents($file_path));
        } catch (PhpParser\Error $exception) {
            $this->logger->critical(
                $this->translator->trans(
                    'Parse error detected in main class of module %module%!',
                    array('%module%' => $name),
                    'Admin.Modules.Notification'));
            return false;
        }

        $logger = $this->logger;
        // -> Even if we do not detect any parse error in the file, we may have issues
        // when trying to load the file. (i.e with additional require_once).
        // -> We use an anonymous function here because if a test is made twice
        // on the same module, the test on require_once would immediately return true
        // (as the file would have already been evaluated).
        $require_correct = function ($name) use ($file_path, $logger) {
            try {
                require_once $file_path;
            } catch (\Exception $e) {
                $logger->error(
                    $this->translator->trans(
                        'Error while loading file of module %module%. %error_message%',
                        array(
                            '%module%' => $name,
                            '%error_message%' =>$e->getMessage()),
                        'Admin.Modules.Notification'));
                return false;
            }
            return true;
        };

        return $require_correct($name);
    }

    /**
     * Check if the module is in the modules folder, with a valid class
     *
     * @param  string $name The technical module name to find
     * @return bool         True if found
     */
    public function isOnDisk($name)
    {
        $path = _PS_MODULE_DIR_.$name.'/'.$name.'.php';
        return file_exists($path);
    }

    /**
     * Check if the module has been enabled on mobile
     * @param string $name The technical module name to check
     * @return int The devices enabled for this module
     */
    private function getDeviceStatus($name)
    {
        $id_shops = (new Context())->getContextListShopID();
        // ToDo: Load list of all installed modules ?

        $result = Db::getInstance()->getRow('SELECT m.`id_module` as `active`, ms.`id_module` as `shop_active`, ms.`enable_device` as `enable_device`
            FROM `'._DB_PREFIX_.'module` m
            LEFT JOIN `'._DB_PREFIX_.'module_shop` ms ON m.`id_module` = ms.`id_module`
            WHERE `name` = "'. pSQL($name) .'"
            AND ms.`id_shop` IN ('.implode(',', array_map('intval', $id_shops)).')');
        if ($result) {
            return (int)$result['enable_device'];
        }
        return false;
    }
}
