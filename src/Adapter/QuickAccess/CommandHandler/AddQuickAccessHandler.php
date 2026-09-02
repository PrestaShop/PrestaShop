<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\QuickAccess\CommandHandler;

use PrestaShop\PrestaShop\Adapter\QuickAccess\Repository\QuickAccessRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\AddQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\CommandHandler\AddQuickAccessHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessConstraintException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;
use PrestaShop\PrestaShop\Core\Language\LocalizedNamesFiller;
use QuickAccess;

#[AsCommandHandler]
class AddQuickAccessHandler implements AddQuickAccessHandlerInterface
{
    public function __construct(
        private readonly QuickAccessRepository $repository,
        private readonly LocalizedNamesFiller $localizedNamesFiller,
    ) {
    }

    public function handle(AddQuickAccessCommand $command): QuickAccessId
    {
        if ($this->repository->hasLink($command->getLink())) {
            throw new QuickAccessConstraintException(
                sprintf('A quick access with link "%s" already exists.', $command->getLink()),
                QuickAccessConstraintException::LINK_ALREADY_EXISTS
            );
        }

        $quickAccess = new QuickAccess();
        // @phpstan-ignore-next-line (ObjectModel multilingual field accepts array at runtime)
        $quickAccess->name = $this->localizedNamesFiller->fill($command->getLocalizedNames());
        $quickAccess->link = $command->getLink();
        $quickAccess->new_window = $command->isNewWindow();

        $this->repository->add($quickAccess);

        return new QuickAccessId((int) $quickAccess->id);
    }
}
