<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command;

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
     *      ['feature_id' => 2, 'custom_value' => 'Custom'], // Create new custom value
     *      ['feature_id' => 2, 'feature_value_id' => 5, 'custom_value' => 'Custom'], // Updates existing custom value
     * ];
     *
     * @param int $productId
     * @param array $featureValues
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
     */
    private function setProductFeatures(array $featureValues): void
    {
        $this->featureValues = [];
        foreach ($featureValues as $featureValue) {
            $this->featureValues[] = new ProductFeatureValue(
                $featureValue['feature_id'],
                !empty($featureValue['feature_value_id']) ? (int) $featureValue['feature_value_id'] : null,
                !empty($featureValue['custom_value']) ? $featureValue['custom_value'] : null,
            );
        }
    }
}
