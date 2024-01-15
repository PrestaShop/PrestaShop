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
