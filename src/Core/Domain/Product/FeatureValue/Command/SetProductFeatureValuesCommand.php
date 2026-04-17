<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidProductFeatureValuesFormatException;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\ValueObject\ProductFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Sets product feature values
 */
class SetProductFeatureValuesCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductFeatureValue[]
     */
    private $featureValues;

    /**
     * Set product feature values, expected format is:
     * $featureValues = [
     *      ['feature_id' => 2, 'feature_value_id' => 3], // Associate predefined feature value
     *      ['feature_id' => 2, 'custom_values' => [1 => 'Custom']], // Create new custom value
     *      ['feature_id' => 2, 'feature_value_id' => 5, 'custom_values' => [1 => 'Custom']], // Updates existing custom value
     * ];
     *
     * @param int $productId
     * @param array $featureValues
     *
     * @throws InvalidProductFeatureValuesFormatException
     * @throws ProductConstraintException
     */
    public function __construct(int $productId, array $featureValues)
    {
        $this->productId = new ProductId($productId);
        $this->setProductFeatures($featureValues);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductFeatureValue[]
     */
    public function getFeatureValues(): array
    {
        return $this->featureValues;
    }

    /**
     * @param array $featureValues
     *
     * @throws InvalidProductFeatureValuesFormatException
     */
    private function setProductFeatures(array $featureValues): void
    {
        if (empty($featureValues)) {
            throw new InvalidProductFeatureValuesFormatException(sprintf(
                'Cannot use empty feature values to remove all use %s instead',
                RemoveAllFeatureValuesFromProductCommand::class
            ));
        }

        $this->featureValues = [];
        foreach ($featureValues as $featureValue) {
            $this->assertFeatureValueFormat($featureValue);
            $this->featureValues[] = new ProductFeatureValue(
                $featureValue['feature_id'],
                !empty($featureValue['feature_value_id']) ? (int) $featureValue['feature_value_id'] : null,
                !empty($featureValue['custom_values']) ? $featureValue['custom_values'] : null
            );
        }
    }

    /**
     * @param array $featureValue
     *
     * @throws InvalidProductFeatureValuesFormatException
     */
    private function assertFeatureValueFormat(array $featureValue): void
    {
        if (empty($featureValue['feature_id'])) {
            throw new InvalidProductFeatureValuesFormatException('Invalid input array, feature_id is expected');
        }
        if (empty($featureValue['feature_value_id']) && empty($featureValue['custom_values'])) {
            throw new InvalidProductFeatureValuesFormatException('Feature value missing, specify a feature_value_id or new custom_values');
        }
    }
}
