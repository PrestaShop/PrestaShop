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

final class FeatureValuesCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['basic']['features']['feature_values'])) {
            return [];
        }

        $featureValuesData = $formData['basic']['features']['feature_values'];
        if (empty($featureValuesData)) {
            return [new RemoveAllFeatureValuesFromProductCommand($productId->getValue())];
        }

        $command = new SetProductFeatureValuesCommand(
            $productId->getValue(),
            $this->formatFeatureValues($featureValuesData)
        );

        return [$command];
    }

    /**
     * @param array $featureValuesData
     *
     * @return array
     */
    private function formatFeatureValues(array $featureValuesData): array
    {
        $featureValues = [];
        foreach ($featureValuesData as $featureValueDatum) {
            if (empty($featureValueDatum['feature_id'])) {
                continue;
            }

            $formattedFeature = ['feature_id' => (int) $featureValueDatum['feature_id']];
            if ($this->hasCustomValues($featureValueDatum)) {
                $formattedFeature['custom_values'] = $featureValueDatum['custom_value'];
                if (!empty($featureValueDatum['custom_value_id'])) {
                    $formattedFeature['feature_value_id'] = (int) $featureValueDatum['custom_value_id'];
                }
            } elseif (!empty($featureValueDatum['feature_value_id'])) {
                $formattedFeature['feature_value_id'] = (int) $featureValueDatum['feature_value_id'];
            }

            $featureValues[] = $formattedFeature;
        }

        return $featureValues;
    }

    /**
     * @param array $featureValueDatum
     *
     * @return bool
     */
    private function hasCustomValues(array $featureValueDatum): bool
    {
        if (empty($featureValueDatum['custom_value'])) {
            return false;
        }

        foreach ($featureValueDatum['custom_value'] as $localizedValue) {
            if (!empty($localizedValue)) {
                return true;
            }
        }

        return false;
    }
}
