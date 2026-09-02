<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Command\AddStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\Command\EditStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Handles submitted supplier form data
 */
class StateFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        /** @var StateId $stateId */
        $stateId = $this->commandBus->handle(new AddStateCommand(
            $data['id_country'],
            $data['id_zone'],
            $data['name'],
            $data['iso_code'],
            $data['active'] ?? false
        ));

        return $stateId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        $command = new EditStateCommand((int) $id);

        $command
            ->setIsoCode($data['iso_code'])
            ->setName($data['name'])
            ->setActive($data['active'] ?? false)
            ->setCountryId($data['id_country'])
            ->setZoneId($data['id_zone']);

        $this->commandBus->handle($command);
    }
}
