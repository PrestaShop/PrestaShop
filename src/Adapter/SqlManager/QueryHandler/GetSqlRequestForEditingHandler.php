<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\EditableSqlRequest;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestForEditing;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler\GetSqlRequestForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use RequestSql;

/**
 * Class GetSqlRequestForEditingHandler.
 *
 * @internal
 */
#[AsQueryHandler]
final class GetSqlRequestForEditingHandler implements GetSqlRequestForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SqlRequestException
     * @throws SqlRequestNotFoundException
     */
    public function handle(GetSqlRequestForEditing $query)
    {
        $entity = $this->loadById($query->getRequestSqlId());

        return $this->buildEditableSqlRequest($entity);
    }

    /**
     * @param SqlRequestId $requestSqlId
     *
     * @return RequestSql
     *
     * @throws SqlRequestNotFoundException
     */
    private function loadById(SqlRequestId $requestSqlId)
    {
        $entity = new RequestSql($requestSqlId->getValue());

        if (0 >= $entity->id) {
            throw new SqlRequestNotFoundException(sprintf('SqlRequest with id "%s" cannot be found', $requestSqlId->getValue()));
        }

        if ((int) $entity->id !== $requestSqlId->getValue()) {
            throw new SqlRequestNotFoundException(sprintf('The retrieved id "%s" does not match requested SqlRequest id "%s"', $entity->id, $requestSqlId->getValue()));
        }

        return $entity;
    }

    /**
     * @param RequestSql $entity
     *
     * @return EditableSqlRequest
     *
     * @throws SqlRequestException
     */
    private function buildEditableSqlRequest(RequestSql $entity)
    {
        return new EditableSqlRequest(
            new SqlRequestId($entity->id),
            $entity->name,
            $entity->sql
        );
    }
}
