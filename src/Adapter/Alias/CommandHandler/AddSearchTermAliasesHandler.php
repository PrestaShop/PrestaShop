<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Alias\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\AddSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\CommandHandler\AddSearchTermAliasesHandlerInterface;

#[AsCommandHandler]
class AddSearchTermAliasesHandler implements AddSearchTermAliasesHandlerInterface
{
    /**
     * @var AliasRepository
     */
    private $aliasRepository;

    /**
     * @param AliasRepository $aliasRepository
     */
    public function __construct(
        AliasRepository $aliasRepository
    ) {
        $this->aliasRepository = $aliasRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddSearchTermAliasesCommand $command): array
    {
        return $this->aliasRepository->addAliases(
            $command->getSearchTerm(),
            $command->getAliases()
        );
    }
}
