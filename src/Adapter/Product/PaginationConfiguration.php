<?php

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginationConfiguration implements DataConfigurationInterface
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
            'products_per_page' => $this->configuration->get('PS_PRODUCTS_PER_PAGE'),
            'default_order_by' => $this->configuration->get('PS_PRODUCTS_ORDER_BY'),
            'default_order_way' => $this->configuration->get('PS_PRODUCTS_ORDER_WAY'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_PRODUCTS_PER_PAGE', (int) $configuration['products_per_page']);
            $this->configuration->set('PS_PRODUCTS_ORDER_BY', (int) $configuration['default_order_by']);
            $this->configuration->set('PS_PRODUCTS_ORDER_WAY', (int) $configuration['default_order_way']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'products_per_page',
            'default_order_by',
            'default_order_way',
        ]);

        $resolver->resolve($configuration);

        return true;
    }
}
