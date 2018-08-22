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

namespace PrestaShop\PrestaShop\Adapter\SqlManager\QueryHandler;

use Db;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestResultForViewingQuery;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler\GetSqlRequestResultForViewingHandlerInterface;
use PrestaShopException;
use RequestSql;

/**
 * Class GetSqlRequestResultForViewingHandler
 */
class GetSqlRequestResultForViewingHandler implements GetSqlRequestResultForViewingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SqlRequestNotFoundException
     * @throws SqlRequestException
     */
    public function handle(GetSqlRequestResultForViewingQuery $query)
    {
        try {
            $id = $query->getRequestSqlId()->getValue();
            $entity = new RequestSql($id);

            if (0 >= $entity->id) {
                throw new SqlRequestNotFoundException(
                    sprintf('SqlRequest with id %s was not found', $id)
                );
            }

            $records = Db::getInstance()->executeS($entity->sql);

            if (empty($records)) {
                return [
                    'headers' => [],
                    'records' => [],
                ];
            }

            $headers = array_keys(reset($records));
            $records = $this->hideSensitiveData($records);

            return [
                'headers' => $headers,
                'records' => $records,
            ];
        } catch (PrestaShopException $e) {
            throw new SqlRequestException(
                'Unexpected error occurred',
                0,
                $e
            );
        }
    }

    /**
     * Replaces sensitive data with placeholder values
     *
     * @param array $records
     *
     * @return array Records with hidden sensitive data
     *
     * @throws PrestaShopException
     */
    private function hideSensitiveData(array $records)
    {
        foreach ($records as $key => $record) {
            foreach ((new RequestSql())->attributes as $sensitiveAttribute => $placeholder) {
                if (isset($record[$sensitiveAttribute])) {
                    $records[$key][$sensitiveAttribute] = $placeholder;
                }
            }
        }

        return $records;
    }
}
