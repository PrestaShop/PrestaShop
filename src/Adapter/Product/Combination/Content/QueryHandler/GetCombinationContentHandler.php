<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Content\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Content\Repository\CombinationContentRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\Query\GetCombinationContent;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\QueryHandler\GetCombinationContentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\QueryResult\CombinationContent;

#[AsQueryHandler]
final class GetCombinationContentHandler implements GetCombinationContentHandlerInterface
{
    public function __construct(
        private readonly CombinationRepository $combinationRepository,
        private readonly CombinationContentRepository $combinationContentRepository,
    ) {
    }

    public function handle(GetCombinationContent $query): CombinationContent
    {
        $combinationId = $query->getCombinationId();
        $localizedValues = $this->combinationContentRepository->getLocalizedValues(
            $combinationId,
            $query->getShopConstraint()->getShopId() ?? $this->combinationRepository->getDefaultShopIdForCombination($combinationId)
        );

        return new CombinationContent(
            $localizedValues['description'],
            $localizedValues['description_short'],
            $localizedValues['meta_description'],
            $localizedValues['meta_title']
        );
    }
}
