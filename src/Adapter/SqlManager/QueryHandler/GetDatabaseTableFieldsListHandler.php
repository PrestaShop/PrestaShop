<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler\GetDatabaseTableFieldsListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\DatabaseTableField;
use RequestSql;

/**
 * Class GetDatabaseTableFieldsListHandler.
 *
 * @internal
 */
#[AsQueryHandler]
final class GetDatabaseTableFieldsListHandler implements GetDatabaseTableFieldsListHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetDatabaseTableFieldsList $query)
    {
        $attributes = (new RequestSql())->getAttributesByTable($query->getTableName());
        $fields = [];

        foreach ($attributes as $attribute) {
            $fields[] = new DatabaseTableField(
                $attribute['Field'],
                $attribute['Type']
            );
        }

        return new DatabaseTableFields($fields);
    }
}
