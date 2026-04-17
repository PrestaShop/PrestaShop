<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\B2b;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;

/**
 * Class B2bFeature checks manages B2B status.
 */
final class B2bFeature implements FeatureInterface
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
        return $this->isActive();
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return (bool) $this->configuration->get('PS_B2B_ENABLE');
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $this->configuration->set('PS_B2B_ENABLE', 1);
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $this->configuration->set('PS_B2B_ENABLE', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function update($status)
    {
        $status ? $this->enable() : $this->disable();
    }
}
