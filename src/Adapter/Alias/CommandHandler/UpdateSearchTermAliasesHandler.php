<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Alias\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\UpdateSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\CommandHandler\UpdateSearchTermAliasesHandlerInterface;

#[AsCommandHandler]
class UpdateSearchTermAliasesHandler implements UpdateSearchTermAliasesHandlerInterface
{
    public function __construct(
        protected AliasRepository $aliasRepository
    ) {
    }

    public function handle(UpdateSearchTermAliasesCommand $command): void
    {
        $this->aliasRepository->deleteAliasesBySearchTerm($command->getOldSearchTerm());
        $this->aliasRepository->addAliases($command->getNewSearchTerm()->getValue(), $command->getAliases());
    }
}
