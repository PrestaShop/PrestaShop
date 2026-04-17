<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                if (!isset($selectField['no_quotes']['parts'])) {
                    continue;
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
