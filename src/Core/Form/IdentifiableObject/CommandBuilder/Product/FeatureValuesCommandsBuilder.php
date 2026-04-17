<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\RemoveAllFeatureValuesFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

final class FeatureValuesCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['details']['features']['feature_collection'])) {
            return [];
        }

        $featuresCollection = $formData['details']['features']['feature_collection'];
        if (empty($featuresCollection)) {
            return [new RemoveAllFeatureValuesFromProductCommand($productId->getValue())];
        }

        $command = new SetProductFeatureValuesCommand(
            $productId->getValue(),
            $this->formatFeatureValues($featuresCollection)
        );

        return [$command];
    }

    /**
     * @param array $featuresCollection
     *
     * @return array
     */
    private function formatFeatureValues(array $featuresCollection): array
    {
        $featureValues = [];
        foreach ($featuresCollection as $featureData) {
            if (empty($featureData['feature_id']) || empty($featureData['feature_values'])) {
                continue;
            }

            $featureId = (int) $featureData['feature_id'];
            foreach ($featureData['feature_values'] as $featureValueData) {
                $formattedFeature = [
                    'feature_id' => $featureId,
                ];

                if (isset($featureValueData['feature_value_id'])) {
                    $formattedFeature['feature_value_id'] = (int) $featureValueData['feature_value_id'];
                }
                if ($this->hasCustomValues($featureValueData)) {
                    $formattedFeature['custom_values'] = $featureValueData['custom_value'];
                }

                $featureValues[] = $formattedFeature;
            }
        }

        return $featureValues;
    }

    /**
     * @param array $featureValueData
     *
     * @return bool
     */
    private function hasCustomValues(array $featureValueData): bool
    {
        if (empty($featureValueData['custom_value']) || empty($featureValueData['is_custom'])) {
            return false;
        }

        foreach ($featureValueData['custom_value'] as $localizedValue) {
            if (!empty($localizedValue)) {
                return true;
            }
        }

        return false;
    }
}
