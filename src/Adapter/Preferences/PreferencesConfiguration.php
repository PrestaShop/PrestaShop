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

namespace PrestaShop\PrestaShop\Adapter\Preferences;

use Cookie;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class will provide Shop Preferences configuration.
 */
class PreferencesConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FeatureFlagRepository
     */
    protected $featureFlagRepository;

    public function __construct(
        Configuration $configuration,
        FeatureFlagRepository $featureFlagRepository
    ) {
        $this->configuration = $configuration;
        $this->featureFlagRepository = $featureFlagRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'enable_ssl' => $this->configuration->getBoolean('PS_SSL_ENABLED'),
            'enable_ssl_everywhere' => $this->configuration->getBoolean('PS_SSL_ENABLED_EVERYWHERE'),
            'enable_token' => $this->configuration->getBoolean('PS_TOKEN_ENABLE'),
            'allow_html_iframes' => $this->configuration->getBoolean('PS_ALLOW_HTML_IFRAME'),
            'use_htmlpurifier' => $this->configuration->getBoolean('PS_USE_HTMLPURIFIER'),
            'price_round_mode' => $this->configuration->get('PS_PRICE_ROUND_MODE'),
            'price_round_type' => $this->configuration->get('PS_ROUND_TYPE'),
            'display_suppliers' => $this->configuration->getBoolean('PS_DISPLAY_SUPPLIERS'),
            'display_manufacturers' => $this->configuration->getBoolean('PS_DISPLAY_MANUFACTURERS'),
            'display_best_sellers' => $this->configuration->getBoolean('PS_DISPLAY_BEST_SELLERS'),
            'multishop_feature_active' => $this->configuration->getBoolean('PS_MULTISHOP_FEATURE_ACTIVE'),
            'shop_activity' => $this->configuration->get('PS_SHOP_ACTIVITY'),
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

        $previousMultistoreFeatureState = $this->configuration->get('PS_MULTISHOP_FEATURE_ACTIVE');

        $shopConstraint = $this->getShopConstraint();

        $this->updateConfigurationValue('PS_SSL_ENABLED', 'enable_ssl', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SSL_ENABLED_EVERYWHERE', 'enable_ssl_everywhere', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_TOKEN_ENABLE', 'enable_token', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_ALLOW_HTML_IFRAME', 'allow_html_iframes', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_USE_HTMLPURIFIER', 'use_htmlpurifier', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_PRICE_ROUND_MODE', 'price_round_mode', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_ROUND_TYPE', 'price_round_type', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_DISPLAY_SUPPLIERS', 'display_suppliers', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_DISPLAY_MANUFACTURERS', 'display_manufacturers', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_DISPLAY_BEST_SELLERS', 'display_best_sellers', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_MULTISHOP_FEATURE_ACTIVE', 'multishop_feature_active', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SHOP_ACTIVITY', 'shop_activity', $configuration, $shopConstraint);

        // Update product page feature automatically based on PS_MULTISHOP_FEATURE_ACTIVE
        if (!$previousMultistoreFeatureState && (bool) $this->configuration->get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            $this->featureFlagRepository->disable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2_MULTI_SHOP);
        }

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
        return (
            $configuration['enable_ssl'] === false
            || $configuration['enable_ssl_everywhere'] === false
        )
            && $this->configuration->get('PS_COOKIE_SAMESITE') === Cookie::SAMESITE_NONE;
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(
                [
                    'enable_ssl',
                    'enable_ssl_everywhere',
                    'enable_token',
                    'allow_html_iframes',
                    'use_htmlpurifier',
                    'price_round_mode',
                    'price_round_type',
                    'display_suppliers',
                    'display_manufacturers',
                    'display_best_sellers',
                    'multishop_feature_active',
                    'shop_activity',
                ]
            );

        return $resolver;
    }
}
