<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PaginationConfiguration is responsible for saving & loading pagination configuration for products.
 */
class PaginationConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'products_per_page',
        'default_order_by',
        'default_order_way',
    ];

    public function __construct(ConfigurationInterface $configuration, Context $shopContext, FeatureInterface $multistoreFeature)
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
            'products_per_page' => (int) $this->configuration->get('PS_PRODUCTS_PER_PAGE', 12, $shopConstraint),
            'default_order_by' => (int) $this->configuration->get('PS_PRODUCTS_ORDER_BY', 0, $shopConstraint),
            'default_order_way' => (int) $this->configuration->get('PS_PRODUCTS_ORDER_WAY', 0, $shopConstraint),
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
            $this->updateConfigurationValue('PS_PRODUCTS_PER_PAGE', 'products_per_page', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCTS_ORDER_BY', 'default_order_by', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PRODUCTS_ORDER_WAY', 'default_order_way', $config, $shopConstraint);
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
            ->setAllowedTypes('products_per_page', 'int')
            ->setAllowedTypes('default_order_by', 'int')
            ->setAllowedTypes('default_order_way', 'int');
    }
}
