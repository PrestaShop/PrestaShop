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

use Doctrine\DBAL\Connection;
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

    public function __construct(Connection $connection, $databasePrefix)
    {
        $this->connection = $connection;
        $this->tableName = $databasePrefix . 'module';
    }

    /**
     * @return array the list of installed modules
     */
    public function getActiveModules()
    {
        if (null === $this->activeModules) {
            $sth = $this->connection->query('SELECT name FROM ' . $this->tableName . ' WHERE active = 1');

            $this->activeModules = $sth->fetchAll(\PDO::FETCH_COLUMN);
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
            $modulesFiles = Finder::create()->directories()->in(__DIR__ . '/../../../modules')->depth(0);
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
