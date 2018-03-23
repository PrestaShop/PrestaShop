<?php

namespace PrestaShop\PrestaShop\Adapter\Preferences;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreferencesConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'enable_ssl' => $this->configuration->get('PS_SSL_ENABLED'),
            'enable_ssl_everywhere' => $this->configuration->get('PS_SSL_ENABLED_EVERYWHERE'),
            'enable_token' => $this->configuration->get('PS_TOKEN_ENABLE'),
            'allow_html_iframes' => $this->configuration->get('PS_ALLOW_HTML_IFRAME'),
            'use_htmlpurifier' => $this->configuration->get('PS_USE_HTMLPURIFIER'),
            'price_round_mode' => $this->configuration->get('PS_PRICE_ROUND_MODE'),
            'price_round_type' => $this->configuration->get('PS_ROUND_TYPE'),
            'price_display_precision' => $this->configuration->get('PS_PRICE_DISPLAY_PRECISION'),
            'display_suppliers' => $this->configuration->get('PS_DISPLAY_SUPPLIERS'),
            'display_best_sellers' => $this->configuration->get('PS_DISPLAY_BEST_SELLERS'),
            'multishop_feature_active' => $this->configuration->get('PS_MULTISHOP_FEATURE_ACTIVE'),
            'shop_activity' => $this->configuration->get('PS_SHOP_ACTIVITY'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_SSL_ENABLED', $configuration['enable_ssl']);
            $this->configuration->set('PS_SSL_ENABLED_EVERYWHERE', $configuration['enable_ssl_everywhere']);
            $this->configuration->set('PS_TOKEN_ENABLE', $configuration['enable_token']);
            $this->configuration->set('PS_ALLOW_HTML_IFRAME', $configuration['allow_html_iframes']);
            $this->configuration->set('PS_USE_HTMLPURIFIER', $configuration['use_htmlpurifier']);
            $this->configuration->set('PS_PRICE_ROUND_MODE', $configuration['price_round_mode']);
            $this->configuration->set('PS_ROUND_TYPE', $configuration['price_round_type']);
            $this->configuration->set('PS_PRICE_DISPLAY_PRECISION', $configuration['price_display_precision']);
            $this->configuration->set('PS_DISPLAY_SUPPLIERS', $configuration['display_suppliers']);
            $this->configuration->set('PS_DISPLAY_BEST_SELLERS', $configuration['display_best_sellers']);
            $this->configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', $configuration['multishop_feature_active']);
            $this->configuration->set('PS_SHOP_ACTIVITY', $configuration['shop_activity']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(
            [
                'enable_ssl',
                'enable_ssl_everywhere',
                'enable_token',
                'allow_html_iframes',
                'use_htmlpurifier',
                'price_round_mode',
                'price_round_type',
                'price_display_precision',
                'display_suppliers',
                'display_best_sellers',
                'multishop_feature_active',
                'shop_activity',
            ]
        );
        $resolver->resolve($configuration);

        return true;
    }
}
