<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\AddSqlRequestHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\CannotAddSqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use PrestaShopException;
use RequestSql;

/**
 * Class AddSqlRequestHandler handles SqlRequest creation command.
 *
 * @internal
 */
#[AsCommandHandler]
final class AddSqlRequestHandler extends AbstractSqlRequestHandler implements AddSqlRequestHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotAddSqlRequestException
     * @throws SqlRequestException
     */
    public function handle(AddSqlRequestCommand $command)
    {
        $this->assertSqlQueryIsValid($command->getSql());

        try {
            $entity = new RequestSql();
            $entity->name = $command->getName();
            $entity->sql = $command->getSql();

            $entity->add();

            if (0 >= $entity->id) {
                throw new CannotAddSqlRequestException(sprintf('Invalid entity id after creation: %s', $entity->id));
            }

            return new SqlRequestId((int) $entity->id);
        } catch (PrestaShopException $e) {
            throw new SqlRequestException('Failed to create SqlRequest', 0, $e);
        }
    }
}
