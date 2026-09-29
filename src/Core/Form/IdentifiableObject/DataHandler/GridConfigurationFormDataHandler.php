<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use LogicException;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\GridView\Command\SaveGridConfigurationCommand;

/**
 * Persists the grid views configuration form data through the command bus. The configuration
 * is an upsert keyed on the current employee and grid, so only the create path is used.
 */
final class GridConfigurationFormDataHandler implements FormDataHandlerInterface
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
    public function create(array $data): mixed
    {
        $this->commandBus->handle(new SaveGridConfigurationCommand(
            (string) ($data['grid_id'] ?? ''),
            (string) ($data['controller_route'] ?? ''),
            (string) ($data['filter_id'] ?? ''),
            (bool) ($data['display_shared_filters'] ?? true),
            (bool) ($data['display_totals'] ?? true)
        ));

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        throw new LogicException('The grid views configuration form is never updated by id, use create() instead.');
    }
}
