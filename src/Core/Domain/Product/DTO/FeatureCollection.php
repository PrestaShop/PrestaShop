<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\DTO;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\CustomizableFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\FeatureValue;

/**
 * Gets a collection from all possible feature values that can be applied for product.
 */
class FeatureCollection
{
    /**
     * @var FeatureValue[]
     */
    private $existingFeatureValues;

    /**
     * @var CustomizableFeatureValue[]
     */
    private $customizableFeatureValues;

    /**
     * @return FeatureValue[]
     */
    public function getExistingFeatureValues(): array
    {
        return $this->existingFeatureValues;
    }

    /**
     * @param FeatureValue $existingFeatureValue
     *
     * @return void
     */
    public function setExistingFeatureValue(FeatureValue $existingFeatureValue): void
    {
        $this->existingFeatureValues[] = $existingFeatureValue;
    }

    /**
     * @return CustomizableFeatureValue[]
     */
    public function getCustomizableFeatureValues(): array
    {
        return $this->customizableFeatureValues;
    }

    /**
     * @param CustomizableFeatureValue $customizableFeatureValues
     *
     * @return void
     */
    public function setCustomizableFeatureValue(CustomizableFeatureValue $customizableFeatureValues): void
    {
        $this->customizableFeatureValues[] = $customizableFeatureValues;
    }
}
