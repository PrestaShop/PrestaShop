<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Alias\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\GetAliasForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryHandler\GetAliasForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\AliasForEditing;

/**
 * Handles the query @see GetAliasForEditing using legacy ObjectModel
 */
#[AsQueryHandler]
class GetAliasForEditingHandler implements GetAliasForEditingHandlerInterface
{
    /**
     * @var AliasRepository
     */
    private $aliasRepository;

    public function __construct(
        AliasRepository $aliasRepository
    ) {
        $this->aliasRepository = $aliasRepository;
    }

    public function handle(GetAliasForEditing $query): AliasForEditing
    {
        $searchTerm = $this->aliasRepository->get($query->getAliasId())->search;

        return new AliasForEditing(
            $this->aliasRepository->getAliasesBySearchTerm($searchTerm),
            $searchTerm
        );
    }
}
