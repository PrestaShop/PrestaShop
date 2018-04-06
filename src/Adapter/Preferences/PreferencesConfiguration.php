<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Preferences;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

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
            'enable_ssl' => $this->configuration->getBoolean('PS_SSL_ENABLED'),
            'enable_ssl_everywhere' => $this->configuration->getBoolean('PS_SSL_ENABLED_EVERYWHERE'),
            'enable_token' => $this->configuration->getBoolean('PS_TOKEN_ENABLE'),
            'allow_html_iframes' => $this->configuration->getBoolean('PS_ALLOW_HTML_IFRAME'),
            'use_htmlpurifier' => $this->configuration->getBoolean('PS_USE_HTMLPURIFIER'),
            'price_round_mode' => $this->configuration->get('PS_PRICE_ROUND_MODE'),
            'price_round_type' => $this->configuration->get('PS_ROUND_TYPE'),
            'price_display_precision' => $this->configuration->get('PS_PRICE_DISPLAY_PRECISION'),
            'display_suppliers' => $this->configuration->getBoolean('PS_DISPLAY_SUPPLIERS'),
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
        return isset(
            $configuration['enable_ssl'],
            $configuration['enable_ssl_everywhere'],
            $configuration['enable_token'],
            $configuration['allow_html_iframes'],
            $configuration['use_htmlpurifier'],
            $configuration['price_round_mode'],
            $configuration['price_round_type'],
            $configuration['price_display_precision'],
            $configuration['display_suppliers'],
            $configuration['display_best_sellers'],
            $configuration['multishop_feature_active'],
            $configuration['shop_activity']
        );
    }
}
