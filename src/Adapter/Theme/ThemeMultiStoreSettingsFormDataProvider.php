<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Theme;

use Configuration;
use PrestaShop\PrestaShop\Core\Form\MultiStoreSettingsFormDataProviderInterface;

/**
 * This class is used to retrieve data which is used in settings form to render multi store fields - its used in
 * Theme & logo page.
 *
 * @internal
 */
final class ThemeMultiStoreSettingsFormDataProvider implements MultiStoreSettingsFormDataProviderInterface
{
    /**
     * @var bool
     */
    private $isShopFeatureUsed;

    /**
     * @var bool
     */
    private $isSingleShopContext;

    /**
     * @param bool $isShopFeatureUsed
     * @param bool $isSingleShopContext
     */
    public function __construct(
        $isShopFeatureUsed,
        $isSingleShopContext
    ) {
        $this->isShopFeatureUsed = $isShopFeatureUsed;
        $this->isSingleShopContext = $isSingleShopContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $isValidShopRestriction = $this->isShopFeatureUsed && $this->isSingleShopContext;

        $isHeaderLogoRestricted = $this->doesConfigurationExistInSingleShopContext('PS_LOGO');
        $isMailRestricted = $this->doesConfigurationExistInSingleShopContext('PS_LOGO_MAIL');
        $isInvoiceLogoRestricted = $this->doesConfigurationExistInSingleShopContext('PS_LOGO_INVOICE');
        $isFaviconRestricted = $this->doesConfigurationExistInSingleShopContext('PS_FAVICON');

        return [
            'header_logo_is_restricted_to_shop' => $isValidShopRestriction && $isHeaderLogoRestricted,
            'mail_logo_is_restricted_to_shop' => $isValidShopRestriction && $isMailRestricted,
            'invoice_logo_is_restricted_to_shop' => $isValidShopRestriction && $isInvoiceLogoRestricted,
            'favicon_is_restricted_to_shop' => $isValidShopRestriction && $isFaviconRestricted,
        ];
    }

    /**
     * Checks if the configuration exists for specific shop context.
     *
     * @param string $configurationKey
     *
     * @return bool
     */
    private function doesConfigurationExistInSingleShopContext($configurationKey)
    {
        return Configuration::isOverridenByCurrentContext($configurationKey);
    }
}
