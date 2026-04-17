<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Alias\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\SearchForSearchTerm;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryHandler\SearchForSearchTermHandlerInterface;

#[AsQueryHandler]
class SearchForSearchTermHandler implements SearchForSearchTermHandlerInterface
{
    public function __construct(protected readonly AliasRepository $aliasRepository)
    {
    }

    /**
     * @param SearchForSearchTerm $query
     *
     * @return string[]
     */
    public function handle(SearchForSearchTerm $query): array
    {
        return array_column(
            $this->aliasRepository->searchSearchTerms($query->getSearchTerm(), $query->getLimit()),
            'search'
        );
    }
}
