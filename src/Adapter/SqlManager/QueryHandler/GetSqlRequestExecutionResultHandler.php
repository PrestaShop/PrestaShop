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

namespace PrestaShop\PrestaShop\Adapter\SqlManager\QueryHandler;

use Db;
use PHPSQLParser\PHPSQLParser;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler\GetSqlRequestExecutionResultHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;
use PrestaShopException;
use RequestSql;

/**
 * Class GetSqlRequestExecutionResultHandler.
 *
 * @internal
 */
#[AsQueryHandler]
final class GetSqlRequestExecutionResultHandler implements GetSqlRequestExecutionResultHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SqlRequestNotFoundException
     * @throws SqlRequestException
     */
    public function handle(GetSqlRequestExecutionResult $query)
    {
        try {
            $id = $query->getSqlRequestId()->getValue();
            $entity = new RequestSql($id);

            if (0 >= $entity->id) {
                throw new SqlRequestNotFoundException(sprintf('SqlRequest with id %s was not found', $id));
            }

            $rows = Db::getInstance()->executeS($entity->sql);

            if (empty($rows)) {
                return new SqlRequestExecutionResult([], []);
            }

            $columns = array_keys(reset($rows));
            $rows = $this->hideSensitiveData($rows, $entity->sql);

            return new SqlRequestExecutionResult(
                $columns,
                $rows
            );
        } catch (PrestaShopException $e) {
            throw new SqlRequestException('Unexpected error occurred', 0, $e);
        }
    }

    /**
     * Replaces sensitive data with placeholder values.
     *
     * @param array $records
     * @param string $query
     *
     * @return array
     */
    private function hideSensitiveData(array $records, string $query): array
    {
        $sensitiveAttributes = $this->getSensitiveAttributes($query);

        foreach ($records as $key => $record) {
            foreach ($sensitiveAttributes as $sensitiveAttribute => $placeholder) {
                if (isset($record[$sensitiveAttribute])) {
                    $records[$key][$sensitiveAttribute] = $placeholder;
                }
            }
        }

        return $records;
    }

    /**
     * Detect from list of sensitive attributes if function or alias are used in the sql query
     * then add alias in the list of sensitives attributes to hide.
     *
     * @param string $query
     *
     * @return array
     */
    private function getSensitiveAttributes(string $query): array
    {
        $sensitiveAttributes = (new RequestSql())->attributes;
        $parser = new PHPSQLParser();
        $parsed = $parser->parse($query);
        foreach ($parsed['SELECT'] as $selectField) {
            if (is_array($selectField['alias'])) {
                $alias = $selectField['alias']['name'];
                while (is_array($selectField['sub_tree'])) {
                    $selectField = $selectField['sub_tree'][0];
                }
                $field = end($selectField['no_quotes']['parts']);
                if (array_key_exists($field, $sensitiveAttributes)) {
                    $alias = str_replace(['"', "'", '`'], '', $alias);
                    $sensitiveAttributes[$alias] = $sensitiveAttributes[$field];
                }
            }
        }

        return $sensitiveAttributes;
    }
}
