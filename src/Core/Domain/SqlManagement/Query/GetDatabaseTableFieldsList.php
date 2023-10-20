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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlManagementConstraintException;

/**
 * Class GetAttributesForDatabaseTableQuery gets list of attributes for given database table name.
 */
class GetDatabaseTableFieldsList
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @param string $tableName
     *
     * @throws SqlManagementConstraintException
     */
    public function __construct($tableName)
    {
        $this->setTableName($tableName);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     *
     * @return self
     *
     * @throws SqlManagementConstraintException
     */
    public function setTableName($tableName)
    {
        if (!is_string($tableName) || empty($tableName)) {
            throw new SqlManagementConstraintException(sprintf('Invalid database table name %s supplied', var_export($tableName, true)), SqlManagementConstraintException::INVALID_DATABASE_TABLE_NAME);
        }

        $this->tableName = $tableName;

        return $this;
    }
}
