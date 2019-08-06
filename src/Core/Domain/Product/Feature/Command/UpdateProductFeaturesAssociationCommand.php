<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Exception\ProductFeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\ValueObject\CustomizableFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\ValueObject\FeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Update product features.
 */
class UpdateProductFeaturesAssociationCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var FeatureValue[]
     */
    private $featureValues;

    /**
     * @var CustomizableFeatureValue[]
     */
    private $customizableFeatureValues;

    /**
     * @param int $productId
     * @param array $features
     *
     * @throws ProductFeatureConstraintException
     */
    public function __construct(int $productId, array $features)
    {
        $this->productId = new ProductId($productId);
        $this->setFeatures($features);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return FeatureValue[]
     */
    public function getFeatureValues(): array
    {
        return $this->featureValues;
    }

    /**
     * @return CustomizableFeatureValue[]
     */
    public function getCustomizableFeatureValues(): array
    {
        return $this->customizableFeatureValues;
    }

    /**
     * @param array $features
     * @throws ProductFeatureConstraintException
     */
    private function setFeatures(array $features): void
    {
        foreach ($features as $feature)  {
            if ($feature['is_customizable']) {
                $this->customizableFeatureValues[] = new CustomizableFeatureValue(
                    $feature['feature_id'],
                    $feature['value']
                );

                continue;
            }

            $this->featureValues[] = new FeatureValue($feature['feature_id'], $feature['feature_value_id']);
        }
    }
}
