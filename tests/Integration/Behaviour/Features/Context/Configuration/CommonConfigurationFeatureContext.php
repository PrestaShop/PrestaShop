<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Country;
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
            $value = $value === 'none' ? 0 : (int) SharedStorage::getStorage()->get($value);
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

    /**
     * @Given /^shop configuration for default shop is set to (.+)$/
     */
    public function shopConfigurationSetDefaultCountry(string $isoCode): void
    {
        $this->setConfiguration('PS_COUNTRY_DEFAULT', Country::getByIso($isoCode));
    }
}
