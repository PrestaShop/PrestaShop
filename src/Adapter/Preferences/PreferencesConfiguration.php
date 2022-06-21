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
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class will provide Shop Preferences configuration.
 */
class PreferencesConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var FeatureFlagRepository
     */
    protected $featureFlagRepository;

    /**
     * AbstractMultistoreConfiguration constructor.
     *
     * @param Configuration $configuration
     * @param FeatureFlagRepository $featureFlagRepository
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     */
    public function __construct(Configuration $configuration, FeatureFlagRepository $featureFlagRepository, Context $shopContext, FeatureInterface $multistoreFeature)
    {
        $this->configuration = $configuration;
        $this->shopContext = $shopContext;
        $this->multistoreFeature = $multistoreFeature;
        $this->featureFlagRepository = $featureFlagRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_ssl' => (bool) $this->configuration->get('PS_SSL_ENABLED', null, $shopConstraint),
            'enable_ssl_everywhere' => (bool) $this->configuration->get('PS_SSL_ENABLED_EVERYWHERE', null, $shopConstraint),
            'enable_token' => (bool) $this->configuration->get('PS_TOKEN_ENABLE', null, $shopConstraint),
            'allow_html_iframes' => (bool) $this->configuration->get('PS_ALLOW_HTML_IFRAME', null, $shopConstraint),
            'use_htmlpurifier' => (bool) $this->configuration->get('PS_USE_HTMLPURIFIER', null, $shopConstraint),
            'price_round_mode' => (int) $this->configuration->get('PS_PRICE_ROUND_MODE', null, $shopConstraint),
            'price_round_type' => (int) $this->configuration->get('PS_ROUND_TYPE', null, $shopConstraint),
            'display_suppliers' => (bool) $this->configuration->get('PS_DISPLAY_SUPPLIERS', null, $shopConstraint),
            'display_manufacturers' => (bool) $this->configuration->get('PS_DISPLAY_MANUFACTURERS', null, $shopConstraint),
            'display_best_sellers' => (bool) $this->configuration->get('PS_DISPLAY_BEST_SELLERS', null, $shopConstraint),
            'multishop_feature_active' => (bool) $this->configuration->get('PS_MULTISHOP_FEATURE_ACTIVE', null, $shopConstraint),
            'shop_activity' => (int) $this->configuration->get('PS_SHOP_ACTIVITY', null, $shopConstraint),
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
            )
            ->setAllowedTypes('enable_ssl', 'bool')
            ->setAllowedTypes('enable_ssl_everywhere', 'bool')
            ->setAllowedTypes('enable_token', 'bool')
            ->setAllowedTypes('allow_html_iframes', 'bool')
            ->setAllowedTypes('use_htmlpurifier', 'bool')
            ->setAllowedTypes('price_round_mode', 'integer')
            ->setAllowedTypes('price_round_type', 'integer')
            ->setAllowedTypes('display_suppliers', 'bool')
            ->setAllowedTypes('display_manufacturers', 'bool')
            ->setAllowedTypes('display_best_sellers', 'bool')
            ->setAllowedTypes('multishop_feature_active', 'bool')
            ->setAllowedTypes('shop_activity', 'integer');

        return $resolver;
    }
}
