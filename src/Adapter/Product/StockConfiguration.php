<?php

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockConfiguration implements DataConfigurationInterface
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
            'allow_ordering_oos' => $this->configuration->get('PS_ORDER_OUT_OF_STOCK'),
            'stock_management' => $this->configuration->get('PS_STOCK_MANAGEMENT'),
            'in_stock_label' => $this->configuration->get('PS_LABEL_IN_STOCK_PRODUCTS'),
            'oos_allowed_backorders' => $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOA'),
            'oos_denied_backorders' => $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD'),
            'delivery_time' => $this->configuration->get('PS_LABEL_DELIVERY_TIME_AVAILABLE'),
            'oos_delivery_time' => $this->configuration->get('PS_LABEL_DELIVERY_TIME_OOSBOA'),
            'pack_stock_management' => $this->configuration->get('PS_PACK_STOCK_TYPE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_ORDER_OUT_OF_STOCK', $config['allow_ordering_oos']);
            $this->configuration->set('PS_STOCK_MANAGEMENT', $config['stock_management']);
            $this->configuration->set('PS_LABEL_IN_STOCK_PRODUCTS', $config['in_stock_label']);
        }

        return $errors;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'allow_ordering_oos',
            'stock_management',
            'in_stock_label',
            'delivery_time',
            'oos_allowed_backorders',
            'oos_delivery_time',
            'oos_denied_backorders',
            'pack_stock_management',
        ]);

        $resolver->resolve($configuration);

        return true;
    }
}
