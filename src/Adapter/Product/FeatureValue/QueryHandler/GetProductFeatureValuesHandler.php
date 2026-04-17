<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\FeatureValue\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query\GetProductFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryHandler\GetProductFeatureValuesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryResult\ProductFeatureValue;

/**
 * Defines contract to handle @var GetProductFeatureValues query
 */
#[AsQueryHandler]
class GetProductFeatureValuesHandler implements GetProductFeatureValuesHandlerInterface
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
    public function handle(GetProductFeatureValues $query): array
    {
        $featureValuesData = $this->featureValueRepository->getAllProductFeatureValues(
            $query->getProductId(),
            $query->getShopId()
        );
        $productFeatureValues = [];
        foreach ($featureValuesData as $featureValuesDatum) {
            $productFeatureValues[] = new ProductFeatureValue(
                (int) $featureValuesDatum['id_feature'],
                (int) $featureValuesDatum['id_feature_value'],
                $featureValuesDatum['localized_values'],
                1 === (int) $featureValuesDatum['custom']
            );
        }

        return $productFeatureValues;
    }
}
