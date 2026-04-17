<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\BulkDeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\BulkDeleteSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotDeleteSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShopException;
use RequestSql;

/**
 * Class BulkDeleteSqlRequestHandler handles bulk delete of SqlRequest command.
 */
#[AsCommandHandler]
final class BulkDeleteSqlRequestHandler implements BulkDeleteSqlRequestHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SqlRequestException
     */
    public function handle(BulkDeleteSqlRequestCommand $command)
    {
        try {
            foreach ($command->getSqlRequestIds() as $sqlRequestId) {
                $entity = new RequestSql($sqlRequestId->getValue());

                if (false === $entity->delete()) {
                    throw new CannotDeleteSqlRequestException(
                        sprintf('Failed to delete SqlRequest with id %d', $sqlRequestId->getValue()),
                        CannotDeleteSqlRequestException::CANNOT_BULK_DELETE
                    );
                }
            }
        } catch (PrestaShopException $e) {
            throw new SqlRequestException('Unexpected error occurred when handling bulk delete SqlRequest', 0, $e);
        }
    }
}
