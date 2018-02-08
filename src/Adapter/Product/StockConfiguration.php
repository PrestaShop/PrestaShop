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
            'in_stock_label' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_ORDER_OUT_OF_STOCK', $config['allow_ordering_oos']);
            $this->configuration->set('PS_STOCK_MANAGEMENT', $config['stock_management']);
            $this->configuration->set('PS_LABEL_IN_STOCK_PRODUCTS', $config['in_stock_label']);
        }
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
        ]);

        $resolver->resolve($configuration);

        return true;
    }
}
