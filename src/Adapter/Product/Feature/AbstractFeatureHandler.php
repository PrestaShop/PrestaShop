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

namespace PrestaShop\PrestaShop\Adapter\Product\Feature;

use Feature;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Exception\FeatureException;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\ValueObject\FeatureId;
use PrestaShopException;

/**
 * Provides reusable methods for feature commands and queries
 */
abstract class AbstractFeatureHandler
{
    /**
     * Gets Feature object model
     *
     * @param FeatureId $featureId
     *
     * @return Feature
     *
     * @throws FeatureException
     * @throws FeatureNotFoundException
     */
    protected function getFeatureById(FeatureId $featureId): Feature
    {
        $featureIdValue = $featureId->getValue();

        try {
            $feature = new Feature($featureIdValue);
        } catch (PrestaShopException $e) {
            throw new FeatureException(
                sprintf('An error occurred when trying to get feature with id %s', $featureIdValue),
                0,
                $e
            );
        }

        if ($feature->id !== $featureIdValue) {
            throw new FeatureNotFoundException(
                sprintf('Feature with id "%s" was not found.', $featureIdValue)
            );
        }

        return $feature;
    }

    /**
     * Deletes feature using legacy object model
     *
     * @param Feature $feature
     *
     * @return bool
     *
     * @throws FeatureException
     */
    protected function deleteFeature(Feature $feature)
    {
        try {
            return $feature->delete();
        } catch (PrestaShopException $e) {
            throw new FeatureException(sprintf(
                'An error occurred when deleting Feature object with id "%s".',
                $feature->id
            ));
        }
    }
}
