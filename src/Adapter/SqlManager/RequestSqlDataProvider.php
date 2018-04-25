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

namespace PrestaShop\PrestaShop\Adapter\SqlManager;

use Db;
use RequestSql;
use Validate;

/**
 * Class RequestSqlDataProvider is responsible for providing data related to Request SQL model
 */
class RequestSqlDataProvider
{
    /**
     * Get Request SQL data by given id
     *
     * @param $id
     *
     * @return array
     */
    public function getRequestSql($id)
    {
        if (!Validate::isLoadedObject($requestSql = new RequestSql($id))) {
            return [];
        }

        return [
            'id' => $requestSql->id,
            'name' => $requestSql->name,
            'sql' => $requestSql->sql,
        ];
    }

    /**
     * Get all database tables
     *
     * @return array
     */
    public function getTables()
    {
        $tables = (new RequestSql())->getTables();

        return $tables;
    }

    /**
     * Get table's columns data
     *
     * @param string $table     Database table name
     *
     * @return array
     */
    public function getTableColumns($table)
    {
        $columns = (new RequestSql())->getAttributesByTable($table);

        return $columns;
    }

    /**
     * Get Request SQL data
     *
     * @param int $id       ID of Request SQL
     *
     * @return array|null   Array of Request SQL results or NULL if Request SQL model does not exist
     */
    public function getRequestSqlResult($id)
    {
        $result = [];

        $requestSql = $this->getRequestSql($id);
        if (empty($requestSql)) {
            return null;
        }

        $columns = [];
        $rows = Db::getInstance()->executeS($requestSql['sql']);
        if (!empty($rows)) {
            $columns = array_keys(reset($rows));
        }

        $result['request_sql'] = $requestSql;
        $result['rows'] = $rows;
        $result['columns'] = $columns;
        $result['attributes'] = (new RequestSql())->attributes;

        return $result;
    }
}
