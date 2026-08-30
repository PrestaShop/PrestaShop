<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\AddQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\EditQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;

final class QuickAccessFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus
    ) {
    }

    public function create(array $data): int
    {
        /** @var QuickAccessId $quickAccessId */
        $quickAccessId = $this->commandBus->handle(new AddQuickAccessCommand(
            $data['name'],
            $data['link'],
            (bool) $data['new_window']
        ));

        return $quickAccessId->getValue();
    }

    public function update($id, array $data): void
    {
        $command = (new EditQuickAccessCommand((int) $id))
            ->setLocalizedNames($data['name'])
            ->setLink($data['link'])
            ->setNewWindow((bool) $data['new_window']);

        $this->commandBus->handle($command);
    }
}
