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

namespace PrestaShopBundle\Kernel;

use Context;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use Symfony\Component\Finder\Finder;

/**
 * Before booting the PrestaShop application in Symfony context,
 * we register every installed modules.
 */
final class ModuleRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string the `modules` table name
     */
    private $tableName;

    /**
     * @var array
     */
    private $activeModules;

    /**
     * @var array
     */
    private $activeModulesPaths;

    /**
     * @var Context
     */
    private $context;

    public function __construct(Connection $connection, $databasePrefix, ContextAdapter $contextAdapter = null)
    {
        if (null !== $contextAdapter) {
            $this->context = $contextAdapter->getContext();
        }
        $this->connection = $connection;
        $this->tableName = $databasePrefix . 'module';
    }

    /**
     * @return array the list of installed modules
     */
    public function getActiveModules()
    {
        if (null === $this->activeModules) {
            if (null === $this->context || null === $this->context->shop) {
                return [];
            }

            $shopIds = $this->context->shop->getContextListShopID();

            if (empty($shopIds)) {
                return [];
            }

            $activeModulesQuery = $this->connection->query(
                'SELECT m.`name` ' .
                'FROM `' . $this->tableName . '` m ' .
                'LEFT JOIN `' . $this->tableName . '_shop` ms ON m.`id_module` = ms.`id_module` ' .
                'WHERE ms.`id_shop` IN (' . implode(',', array_map('intval', $shopIds)) . ')'
            );

            $this->activeModules = $activeModulesQuery->fetchAll(\PDO::FETCH_COLUMN);
        }

        return $this->activeModules;
    }

    /**
     * Returns installed module file paths.
     *
     * @return array
     */
    public function getActiveModulesPaths()
    {
        if (null === $this->activeModulesPaths) {
            $this->activeModulesPaths = [];
            $modulesFiles = Finder::create()->directories()->in(_PS_MODULE_DIR_)->depth(0);
            $activeModules = $this->getActiveModules();

            foreach ($modulesFiles as $moduleFile) {
                if (in_array($moduleFile->getFilename(), $activeModules)) {
                    $this->activeModulesPaths[] = $moduleFile->getPathname();
                }
            }
        }

        return $this->activeModulesPaths;
    }
}
