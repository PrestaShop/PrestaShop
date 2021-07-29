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

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tools;

class CommonConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^shop configuration for "(.+)" is set to (.+)$/
     */
    public function shopConfigurationOfIsSetTo(string $index, $value): void
    {
        if ($index === 'PS_PRICE_ROUND_MODE') {
            Tools::$round_mode = null;
        }
        if ($index === 'PS_ECOTAX_TAX_RULES_GROUP_ID') {
            $value = (int) SharedStorage::getStorage()->get($value);
        }
        $this->setConfiguration($index, $value);
    }

    /**
     * @Given /^order out of stock products is allowed$/
     */
    public function allowOrderOutOfStock(): void
    {
        $this->setConfiguration('PS_ORDER_OUT_OF_STOCK', 1);
    }

    /**
     * @Given /^shipping handling fees are set to (\d+\.\d+)$/
     */
    public function setShippingHandlingFees($value): void
    {
        $this->setConfiguration('PS_SHIPPING_HANDLING', $value);
    }

    /**
     * @Given /^groups feature is activated$/
     */
    public function activateGroupFeature()
    {
        Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', '1');
    }

    /**
     * @Given /^customization feature is (enabled|disabled)$/
     *
     * @Transform(enabled|disabled)
     */
    public function toggleCustomizationFeature(string $status)
    {
        $status = PrimitiveUtils::castStringBooleanIntoBoolean($status);
        Configuration::set(
            'PS_CUSTOMIZATION_FEATURE_ACTIVE',
            $status
        );
    }

    /**
     * @Given /^search indexation feature is (enabled|disabled)$/
     *
     * @Transform(enabled|disabled)
     *
     * @param string $status
     */
    public function toggleSearchIndexation(string $status): void
    {
        $status = PrimitiveUtils::castStringBooleanIntoBoolean($status);
        Configuration::set(
            'PS_SEARCH_INDEXATION',
            $status
        );
    }
}
