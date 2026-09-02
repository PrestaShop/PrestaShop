<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\AddZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\EditZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;

/**
 * Handles submitted zone form data.
 */
final class ZoneFormDataHandler implements FormDataHandlerInterface
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
     * Create object from form data.
     *
     * @param array $data
     *
     * @return int
     */
    public function create(array $data): int
    {
        if (empty($data['shop_association'])) {
            $data['shop_association'] = [];
        }

        $addZoneCommand = new AddZoneCommand($data['name'], $data['enabled'], $data['shop_association']);
        $zoneId = $this->commandBus->handle($addZoneCommand);

        return $zoneId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws ZoneException
     */
    public function update($id, array $data): void
    {
        $command = (new EditZoneCommand($id))
            ->setName((string) $data['name'])
            ->setEnabled((bool) $data['enabled']);

        if (isset($data['shop_association'])) {
            $shopAssociation = $data['shop_association'] ?: [];
            $shopAssociation = array_map('intval', $shopAssociation);

            $command->setShopAssociation($shopAssociation);
        }

        $this->commandBus->handle($command);
    }
}
