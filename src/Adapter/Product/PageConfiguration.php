<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PageConfiguration is responsible for saving & loading product page configuration.
 */
class PageConfiguration implements DataConfigurationInterface
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
            'display_quantities' => $this->configuration->getBoolean('PS_DISPLAY_QTIES'),
            'allow_add_variant_to_cart_from_listing' => $this->configuration->getBoolean('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'use_combination_image_in_listing' => $this->configuration->getBoolean('PS_USE_COMBINATION_IMAGE_IN_LISTING'),
            'attribute_anchor_separator' => $this->configuration->get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'),
            'display_discount_price' => $this->configuration->getBoolean('PS_DISPLAY_DISCOUNT_PRICE'),
            'display_amount_in_cart' => $this->configuration->getBoolean('PS_DISPLAY_AMOUNT_IN_CART'),
            'feature_values_order' => $this->configuration->get('PS_FEATURE_VALUES_ORDER'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_DISPLAY_QTIES', (int) $config['display_quantities']);
            $this->configuration->set('PS_ATTRIBUTE_CATEGORY_DISPLAY', (int) $config['allow_add_variant_to_cart_from_listing']);
            $this->configuration->set('PS_USE_COMBINATION_IMAGE_IN_LISTING', (int) $config['use_combination_image_in_listing']);
            $this->configuration->set('PS_ATTRIBUTE_ANCHOR_SEPARATOR', $config['attribute_anchor_separator']);
            $this->configuration->set('PS_DISPLAY_DISCOUNT_PRICE', (int) $config['display_discount_price']);
            $this->configuration->set('PS_DISPLAY_AMOUNT_IN_CART', (int) $config['display_amount_in_cart']);
            $this->configuration->set('PS_FEATURE_VALUES_ORDER', $config['feature_values_order']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'display_quantities',
            'allow_add_variant_to_cart_from_listing',
            'use_combination_image_in_listing',
            'attribute_anchor_separator',
            'display_discount_price',
            'display_amount_in_cart',
            'feature_values_order',
        ]);

        $resolver->resolve($config);

        return true;
    }
}
