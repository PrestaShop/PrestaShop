<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StockConfiguration is responsible for saving & loading products stock configuration.
 */
class StockConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'allow_ordering_oos',
        'stock_management',
        'in_stock_label',
        'delivery_time',
        'oos_allowed_backorders',
        'oos_delivery_time',
        'oos_denied_backorders',
        'pack_stock_management',
        'oos_show_label_listing_pages',
        'display_last_quantities',
        'display_unavailable_attributes',
    ];

    public function __construct(Configuration $configuration, Context $shopContext, FeatureInterface $multistoreFeature)
    {
        parent::__construct($configuration, $shopContext, $multistoreFeature);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'allow_ordering_oos' => (bool) $this->configuration->get('PS_ORDER_OUT_OF_STOCK', false, $shopConstraint),
            'stock_management' => (bool) $this->configuration->get('PS_STOCK_MANAGEMENT', false, $shopConstraint),
            'in_stock_label' => (array) $this->configuration->get('PS_LABEL_IN_STOCK_PRODUCTS', [], $shopConstraint),
            'oos_allowed_backorders' => (array) $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOA', [], $shopConstraint),
            'oos_denied_backorders' => (array) $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD', [], $shopConstraint),
            'delivery_time' => (array) $this->configuration->get('PS_LABEL_DELIVERY_TIME_AVAILABLE', [], $shopConstraint),
            'oos_delivery_time' => (array) $this->configuration->get('PS_LABEL_DELIVERY_TIME_OOSBOA', [], $shopConstraint),
            'pack_stock_management' => (int) $this->configuration->get('PS_PACK_STOCK_TYPE', 0, $shopConstraint),
            'oos_show_label_listing_pages' => (bool) $this->configuration->get('PS_SHOW_LABEL_OOS_LISTING_PAGES', false, $shopConstraint),
            'display_last_quantities' => (int) $this->configuration->get('PS_LAST_QTIES', 0, $shopConstraint),
            'display_unavailable_attributes' => (bool) $this->configuration->get('PS_DISP_UNAVAILABLE_ATTR', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_ORDER_OUT_OF_STOCK', 'allow_ordering_oos', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_STOCK_MANAGEMENT', 'stock_management', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_IN_STOCK_PRODUCTS', 'in_stock_label', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_OOS_PRODUCTS_BOA', 'oos_allowed_backorders', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_OOS_PRODUCTS_BOD', 'oos_denied_backorders', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_DELIVERY_TIME_AVAILABLE', 'delivery_time', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_DELIVERY_TIME_OOSBOA', 'oos_delivery_time', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PACK_STOCK_TYPE', 'pack_stock_management', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SHOW_LABEL_OOS_LISTING_PAGES', 'oos_show_label_listing_pages', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LAST_QTIES', 'display_last_quantities', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_DISP_UNAVAILABLE_ATTR', 'display_unavailable_attributes', $config, $shopConstraint);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('allow_ordering_oos', 'bool')
            ->setAllowedTypes('stock_management', 'bool')
            ->setAllowedTypes('in_stock_label', 'array')
            ->setAllowedTypes('delivery_time', 'array')
            ->setAllowedTypes('oos_allowed_backorders', 'array')
            ->setAllowedTypes('oos_delivery_time', 'array')
            ->setAllowedTypes('oos_denied_backorders', 'array')
            ->setAllowedTypes('pack_stock_management', 'int')
            ->setAllowedTypes('oos_show_label_listing_pages', 'bool')
            ->setAllowedTypes('display_last_quantities', 'int')
            ->setAllowedTypes('display_unavailable_attributes', 'bool');
    }
}
