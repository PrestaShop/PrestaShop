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
 * Class PageConfiguration is responsible for saving & loading product page configuration.
 */
class PageConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'display_quantities',
        'allow_add_variant_to_cart_from_listing',
        'use_combination_image_in_listing',
        'attribute_anchor_separator',
        'display_discount_price',
        'display_amount_in_cart',
        'feature_values_order',
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
            'display_quantities' => (bool) $this->configuration->get('PS_DISPLAY_QTIES', false, $shopConstraint),
            'allow_add_variant_to_cart_from_listing' => (bool) $this->configuration->get('PS_ATTRIBUTE_CATEGORY_DISPLAY', false, $shopConstraint),
            'use_combination_image_in_listing' => (bool) $this->configuration->get('PS_USE_COMBINATION_IMAGE_IN_LISTING', false, $shopConstraint),
            'attribute_anchor_separator' => (string) $this->configuration->get('PS_ATTRIBUTE_ANCHOR_SEPARATOR', '-', $shopConstraint),
            'display_discount_price' => (bool) $this->configuration->get('PS_DISPLAY_DISCOUNT_PRICE', false, $shopConstraint),
            'display_amount_in_cart' => (bool) $this->configuration->get('PS_DISPLAY_AMOUNT_IN_CART', false, $shopConstraint),
            'feature_values_order' => (string) $this->configuration->get('PS_FEATURE_VALUES_ORDER', 'name', $shopConstraint),
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
            $this->updateConfigurationValue('PS_DISPLAY_QTIES', 'display_quantities', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_ATTRIBUTE_CATEGORY_DISPLAY', 'allow_add_variant_to_cart_from_listing', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_USE_COMBINATION_IMAGE_IN_LISTING', 'use_combination_image_in_listing', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_ATTRIBUTE_ANCHOR_SEPARATOR', 'attribute_anchor_separator', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_DISPLAY_DISCOUNT_PRICE', 'display_discount_price', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_DISPLAY_AMOUNT_IN_CART', 'display_amount_in_cart', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_FEATURE_VALUES_ORDER', 'feature_values_order', $config, $shopConstraint);
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
            ->setAllowedTypes('display_quantities', 'bool')
            ->setAllowedTypes('allow_add_variant_to_cart_from_listing', 'bool')
            ->setAllowedTypes('use_combination_image_in_listing', 'bool')
            ->setAllowedTypes('attribute_anchor_separator', 'string')
            ->setAllowedTypes('display_discount_price', 'bool')
            ->setAllowedTypes('display_amount_in_cart', 'bool')
            ->setAllowedTypes('feature_values_order', 'string');
    }
}
