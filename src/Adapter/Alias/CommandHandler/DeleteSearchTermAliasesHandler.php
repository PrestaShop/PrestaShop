<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Alias\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\DeleteSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\CommandHandler\DeleteSearchTermAliasesHandlerInterface;

#[AsCommandHandler]
class DeleteSearchTermAliasesHandler implements DeleteSearchTermAliasesHandlerInterface
{
    public function __construct(private readonly AliasRepository $aliasRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteSearchTermAliasesCommand $command): void
    {
        $this->aliasRepository->deleteAliasesBySearchTerm($command->getSearchTerm());
    }
}
