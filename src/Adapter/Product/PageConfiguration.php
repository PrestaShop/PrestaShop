<?php

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            'display_quantities' => $this->configuration->get('PS_DISPLAY_QTIES'),
            'display_last_quantities' => $this->configuration->get('PS_LAST_QTIES'),
            'display_unavailable_attributes' => $this->configuration->get('PS_DISP_UNAVAILABLE_ATTR'),
            'allow_add_variant_to_cart_from_listing' => $this->configuration->get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'attribute_anchor_separator' => $this->configuration->get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'),
            'display_discount_price' => $this->configuration->get('PS_DISPLAY_DISCOUNT_PRICE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_DISPLAY_QTIES', (bool) $config['display_quantities']);
            $this->configuration->set('PS_LAST_QTIES', (int) $config['display_last_quantities']);
            $this->configuration->set('PS_DISP_UNAVAILABLE_ATTR', (bool) $config['display_unavailable_attributes']);
            $this->configuration->set('PS_ATTRIBUTE_CATEGORY_DISPLAY', (bool) $config['allow_add_variant_to_cart_from_listing']);
            $this->configuration->set('PS_ATTRIBUTE_ANCHOR_SEPARATOR', (bool) $config['attribute_anchor_separator']);
            $this->configuration->set('PS_DISPLAY_DISCOUNT_PRICE', (bool) $config['display_discount_price']);
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
            'display_last_quantities',
            'display_unavailable_attributes',
            'allow_add_variant_to_cart_from_listing',
            'attribute_anchor_separator',
            'display_discount_price',
        ]);

        $resolver->resolve($config);

        return true;
    }
}
