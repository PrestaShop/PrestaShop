<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Delivery;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class manages Order delivery slip options configuration.
 */
final class SlipOptionsConfiguration implements DataConfigurationInterface
{
    public const PREFIX = 'PS_DELIVERY_PREFIX';
    public const NUMBER = 'PS_DELIVERY_NUMBER';
    public const ENABLE_PRODUCT_IMAGE = 'PS_PDF_IMG_DELIVERY';

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns configuration used to manage slip options in back office.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'prefix' => $this->configuration->get(self::PREFIX),
            'number' => $this->configuration->getInt(self::NUMBER),
            'enable_product_image' => $this->configuration->getBoolean(self::ENABLE_PRODUCT_IMAGE),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set(self::PREFIX, $configuration['prefix']);
            $this->configuration->set(self::NUMBER, $configuration['number']);
            $this->configuration->set(self::ENABLE_PRODUCT_IMAGE, $configuration['enable_product_image']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['prefix'],
            $configuration['number'],
            $configuration['enable_product_image']
        );
    }
}
