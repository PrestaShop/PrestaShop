<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\EditSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotEditSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShopException;
use RequestSql;

/**
 * Class EditSqlRequestHandler is responsible for updating SqlRequest.
 *
 * @internal
 */
#[AsCommandHandler]
final class EditSqlRequestHandler extends AbstractSqlRequestHandler implements EditSqlRequestHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param EditSqlRequestCommand $command
     *
     * @throws CannotEditSqlRequestException
     * @throws SqlRequestException
     * @throws SqlRequestNotFoundException
     */
    public function handle(EditSqlRequestCommand $command)
    {
        $this->assertSqlQueryIsValid($command->getSql());

        try {
            $entity = new RequestSql($command->getSqlRequestId()->getValue());

            if (0 >= $entity->id) {
                throw new SqlRequestNotFoundException(sprintf('SqlRequest with id "%s" was not found for edit', $command->getSqlRequestId()->getValue()));
            }

            if (null !== $command->getName()) {
                $entity->name = $command->getName();
            }

            if (null !== $command->getSql()) {
                $entity->sql = $command->getSql();
            }

            if (false === $entity->update()) {
                throw new CannotEditSqlRequestException(sprintf('Error occurred when updating SqlRequest with id "%s"', $command->getSqlRequestId()->getValue()));
            }
        } catch (PrestaShopException) {
            throw new SqlRequestException(sprintf('Error occurred when updating SqlRequest with id "%s"', $command->getSqlRequestId()->getValue()));
        }
    }
}
