<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchProductCombinations;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombination;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationsCollection;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;

#[AsQueryHandler]
class SearchProductCombinationsHandler implements SearchProductCombinationsHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var CombinationNameBuilderInterface
     */
    private $combinationNameBuilder;

    /**
     * @param CombinationRepository $combinationRepository
     * @param CombinationNameBuilderInterface $combinationNameBuilder
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        CombinationNameBuilderInterface $combinationNameBuilder
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->combinationNameBuilder = $combinationNameBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SearchProductCombinations $query): ProductCombinationsCollection
    {
        $combinationsAttributesInformation = $this->combinationRepository->searchProductCombinations(
            $query->getProductId(),
            $query->getLanguageId(),
            $query->getShopConstraint(),
            $query->getSearchPhrase(),
            $query->getLimit()
        );

        $productCombinations = [];
        foreach ($combinationsAttributesInformation as $combinationId => $combinationAttributesInformation) {
            $productCombinations[] = new ProductCombination(
                $combinationId,
                $this->combinationNameBuilder->buildName($combinationAttributesInformation)
            );
        }

        return new ProductCombinationsCollection($productCombinations);
    }
}
