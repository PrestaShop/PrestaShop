<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\FeatureValue\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Query\GetCombinationFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\QueryHandler\GetCombinationFeatureValuesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\QueryResult\CombinationFeatureValue;

/**
 * Handles @see GetCombinationFeatureValues query
 */
#[AsQueryHandler]
class GetCombinationFeatureValuesHandler implements GetCombinationFeatureValuesHandlerInterface
{
    /**
     * @var FeatureValueRepository
     */
    private $featureValueRepository;

    public function __construct(FeatureValueRepository $featureValueRepository)
    {
        $this->featureValueRepository = $featureValueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetCombinationFeatureValues $query): array
    {
        $featureValuesData = $this->featureValueRepository->getAllCombinationFeatureValues(
            $query->getCombinationId(),
            $query->getShopId()
        );
        $combinationFeatureValues = [];
        foreach ($featureValuesData as $featureValuesDatum) {
            $combinationFeatureValues[] = new CombinationFeatureValue(
                (int) $featureValuesDatum['id_feature'],
                (int) $featureValuesDatum['id_feature_value'],
                $featureValuesDatum['localized_values'],
                1 === (int) $featureValuesDatum['custom']
            );
        }

        return $combinationFeatureValues;
    }
}
