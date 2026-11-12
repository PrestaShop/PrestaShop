<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\AddGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\EditGridViewCommand;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;

/**
 * Persists the grid view form data through the command bus.
 */
final class GridViewFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        /** @var GridViewId $gridViewId */
        $gridViewId = $this->commandBus->handle(new AddGridViewCommand(
            (string) ($data['grid_id'] ?? ''),
            (string) ($data['name'] ?? ''),
            (bool) ($data['shared'] ?? false),
            (string) ($data['controller_route'] ?? ''),
            (string) ($data['filter_id'] ?? ''),
            isset($data['grid_state']) ? (string) $data['grid_state'] : null,
            (array) ($data['dynamic_date_rules'] ?? [])
        ));

        return $gridViewId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        $editGridViewCommand = (new EditGridViewCommand((int) $id))
            ->setName((string) ($data['name'] ?? ''))
            ->setShared((bool) ($data['shared'] ?? false))
            ->setDynamicDateRules((array) ($data['dynamic_date_rules'] ?? []));

        $this->commandBus->handle($editGridViewCommand);
    }
}
