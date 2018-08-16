<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager;

use PrestaShop\PrestaShop\Adapter\Entity\RequestSql;

/**
 * Class RequestSqlManager for managing legacy RequestSqlCore model
 */
class RequestSqlManager
{
    /**
     * Create or updating existing RequestSqlCore model from given data
     *
     * @param array $data RequestSql data
     *
     * @return array Errors if any
     */
    public function createOrUpdateFromData(array $data)
    {
        $id = isset($data['id']) ? (int) $data['id'] : null;

        $requestSql = new RequestSql($id);
        $requestSql->name = $data['name'];
        $requestSql->sql = $data['sql'];

        if (true !== $error = $requestSql->validateFields(false, true)) {
            return [$error];
        }

        $requestSql->save();

        return [];
    }

    /**
     * Delete Request SQL
     *
     * @param int[] $requestSqlIds ID of Request SQL
     *
     * @return array Errors if any
     */
    public function delete(array $requestSqlIds)
    {
        $errors = [];

        foreach ($requestSqlIds as $id) {
            $requestSql = new RequestSql($id);

            if (!$requestSql->delete()) {
                $errors[] = [
                    'key' => 'Can\'t delete #%id%',
                    'parameters' => [
                        '%id%' => $id
                    ],
                    'domain' => 'Admin.Notifications.Error',
                ];
            }
        }

        return $errors;
    }
}
