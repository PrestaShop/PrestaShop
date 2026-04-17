<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PaginationConfiguration is responsible for saving & loading pagination configuration for products.
 */
class PaginationConfiguration implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
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
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_PRODUCTS_PER_PAGE', (int) $config['products_per_page']);
            $this->configuration->set('PS_PRODUCTS_ORDER_BY', (int) $config['default_order_by']);
            $this->configuration->set('PS_PRODUCTS_ORDER_WAY', (int) $config['default_order_way']);
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
