<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Feature;

use PrestaShop\PrestaShop\Adapter\Entity\Shop;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;

/**
 * Class MultistoreFeature provides data about multishop feature usage.
 *
 * @internal
 */
class MultistoreFeature implements FeatureInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function isUsed()
    {
        // internally it checks if feature is active
        // and at least 2 shops exist
        return Shop::isFeatureActive();
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return (bool) $this->configuration->get(MultistoreConfig::FEATURE_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $this->configuration->set(MultistoreConfig::FEATURE_STATUS, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $this->configuration->set(MultistoreConfig::FEATURE_STATUS, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function update($status)
    {
        $status ? $this->enable() : $this->disable();
    }
}
