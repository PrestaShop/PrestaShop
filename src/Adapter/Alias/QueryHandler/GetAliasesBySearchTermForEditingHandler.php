<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Alias\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\GetAliasesBySearchTermForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryHandler\GetAliasesBySearchTermForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\AliasForEditing;

/**
 * Handle the query @see GetAliasesBySearchTermForEditing using legacy ObjectModel
 */
#[AsQueryHandler]
class GetAliasesBySearchTermForEditingHandler implements GetAliasesBySearchTermForEditingHandlerInterface
{
    public function __construct(
        private readonly AliasRepository $aliasRepository
    ) {
    }

    public function handle(GetAliasesBySearchTermForEditing $query): AliasForEditing
    {
        $aliases = $this->aliasRepository->getAliasesBySearchTerm($query->getSearchTerm());

        return new AliasForEditing($aliases, $query->getSearchTerm());
    }
}
