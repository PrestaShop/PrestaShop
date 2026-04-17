<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\DeleteSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\DeleteSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotDeleteSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShopException;
use RequestSql;

/**
 * Class DeleteSqlRequestHandler.
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteSqlRequestHandler implements DeleteSqlRequestHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteSqlRequestException
     * @throws SqlRequestNotFoundException
     * @throws SqlRequestException
     */
    public function handle(DeleteSqlRequestCommand $command)
    {
        $entityId = $command->getSqlRequestId()->getValue();

        try {
            $entity = new RequestSql($entityId);

            if (0 >= $entity->id) {
                throw new SqlRequestNotFoundException(sprintf('SqlRequest with id "%s" was not found for edit', var_export($entityId, true)));
            }

            if (false === $entity->delete()) {
                throw new CannotDeleteSqlRequestException(sprintf('Could not delete SqlRequest with id %s', var_export($entityId, true)), CannotDeleteSqlRequestException::CANNOT_SINGLE_DELETE);
            }
        } catch (PrestaShopException $e) {
            throw new SqlRequestException(sprintf('Unexpected error occurred when deleting SqlRequest with id %s', var_export($entityId, true)), 0, $e);
        }
    }
}
