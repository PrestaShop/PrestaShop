<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Preferences;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\Enum\ShopModeEnum;
use PrestaShop\PrestaShop\Core\Feature\ShopModeFeature;
use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\General\PreferencesType;
use PrestaShopLogger;

/**
 * This class will provide Shop Preferences configuration.
 */
class PreferencesConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly Configuration $configuration,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopMode = $this->configuration->getEnum(
            ShopModeFeature::CONFIGURATION_NAME,
            ShopModeEnum::class,
            ShopModeFeature::DEFAULT_SHOP_MODE
        );

        return [
            'enable_ssl' => $this->configuration->getBoolean('PS_SSL_ENABLED'),
            'enable_token' => $this->configuration->getBoolean('PS_TOKEN_ENABLE'),
            PreferencesType::SHOP_MODE => $shopMode,
            'allow_html_iframes' => $this->configuration->getBoolean('PS_ALLOW_HTML_IFRAME'),
            'use_htmlpurifier' => $this->configuration->getBoolean('PS_USE_HTMLPURIFIER'),
            'price_round_mode' => $this->configuration->get('PS_PRICE_ROUND_MODE'),
            'price_round_type' => $this->configuration->get('PS_ROUND_TYPE'),
            'display_suppliers' => $this->configuration->getBoolean('PS_DISPLAY_SUPPLIERS'),
            'display_manufacturers' => $this->configuration->getBoolean('PS_DISPLAY_MANUFACTURERS'),
            'display_best_sellers' => $this->configuration->getBoolean('PS_DISPLAY_BEST_SELLERS'),
            'multishop_feature_active' => $this->configuration->getBoolean('PS_MULTISHOP_FEATURE_ACTIVE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if (false === $this->validateConfiguration($configuration)) {
            return [
                [
                    'key' => 'Invalid configuration',
                    'domain' => 'Admin.Notifications.Warning',
                    'parameters' => [],
                ],
            ];
        }

        if ($this->validateSameSiteConfiguration($configuration)) {
            return [
                [
                    'key' => 'Cannot disable SSL configuration due to the Cookie SameSite=None.',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => [],
                ],
            ];
        }

        /** @var ShopModeEnum $newShopModeValue */
        $newShopModeValue = $configuration[PreferencesType::SHOP_MODE];

        /** @var ShopModeEnum $oldShopModeValue */
        $oldShopModeValue = $this->configuration->getEnum(
            ShopModeFeature::CONFIGURATION_NAME,
            ShopModeEnum::class,
            ShopModeFeature::DEFAULT_SHOP_MODE
        );

        if ($oldShopModeValue !== $newShopModeValue) {
            PrestaShopLogger::addLog(
                sprintf('Shop mode updated: from "%s" to "%s"', $oldShopModeValue->value, $newShopModeValue->value),
                1,
                null,
                'Configuration',
                0,
                true
            );
        }

        $this->configuration->set('PS_SSL_ENABLED', $configuration['enable_ssl']);
        $this->configuration->set('PS_TOKEN_ENABLE', $configuration['enable_token']);
        $this->configuration->set(ShopModeFeature::CONFIGURATION_NAME, $newShopModeValue->value);
        $this->configuration->set('PS_ALLOW_HTML_IFRAME', $configuration['allow_html_iframes']);
        $this->configuration->set('PS_USE_HTMLPURIFIER', $configuration['use_htmlpurifier']);
        $this->configuration->set('PS_PRICE_ROUND_MODE', $configuration['price_round_mode']);
        $this->configuration->set('PS_ROUND_TYPE', $configuration['price_round_type']);
        $this->configuration->set('PS_DISPLAY_SUPPLIERS', $configuration['display_suppliers']);
        $this->configuration->set('PS_DISPLAY_MANUFACTURERS', $configuration['display_manufacturers']);
        $this->configuration->set('PS_DISPLAY_BEST_SELLERS', $configuration['display_best_sellers']);
        $this->configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', $configuration['multishop_feature_active']);

        return [];
    }

    /**
     * Validate the SSL configuration can be disabled if the SameSite Cookie
     * is not settled to None
     *
     * @param array $configuration
     *
     * @return bool
     */
    protected function validateSameSiteConfiguration(array $configuration): bool
    {
        return $configuration['enable_ssl'] === false && $this->configuration->get('PS_COOKIE_SAMESITE') === CookieOptions::SAMESITE_NONE;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['enable_ssl'],
            $configuration['enable_token'],
            $configuration[PreferencesType::SHOP_MODE],
            $configuration['allow_html_iframes'],
            $configuration['use_htmlpurifier'],
            $configuration['price_round_mode'],
            $configuration['price_round_type'],
            $configuration['display_suppliers'],
            $configuration['display_manufacturers'],
            $configuration['display_best_sellers'],
            $configuration['multishop_feature_active']
        );
    }
}
