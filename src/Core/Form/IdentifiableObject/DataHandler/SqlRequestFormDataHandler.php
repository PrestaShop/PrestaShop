<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Creates or updates SqlRequest objects using form data.
 */
final class SqlRequestFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $bus
     */
    public function __construct(CommandBusInterface $bus)
    {
        $this->commandBus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        /** @var SqlRequestId $sqlRequestId */
        $sqlRequestId = $this->commandBus->handle(new AddSqlRequestCommand($data['name'], $data['sql']));

        return $sqlRequestId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $sqlRequestId = new SqlRequestId($id);

        $command = (new EditSqlRequestCommand($sqlRequestId))
            ->setName($data['name'])
            ->setSql($data['sql']);

        $this->commandBus->handle($command);
    }
}
