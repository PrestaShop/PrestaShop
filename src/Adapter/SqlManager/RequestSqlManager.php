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

/**
 * Class RequestSqlManager for managing legacy RequestSqlCore model
 */
class RequestSqlManager
{
    /**
     * Create or updating existing RequestSqlCore model from given data
     *
     * @param array $data
     *
     * @return array
     */
    public function createOrUpdateFromData(array $data)
    {
        $id = isset($data['id']) ? (int) $data['id'] : null;

        $requestSql = new \RequestSql($id);
        $requestSql->name = $data['name'];
        $requestSql->sql = $data['sql'];

        if (true !== $result = $requestSql->validateFields(false, true)) {
            return [$result];
        }

        $requestSql->save();

        return [];
    }

    /**
     * Delete Request SQL
     *
     * @param int $id   ID of Request SQL
     *
     * @return bool     True on success or False otherwise
     */
    public function delete($id)
    {
        $requestSql = new \RequestSql($id);

        return $requestSql->delete();
    }
}
