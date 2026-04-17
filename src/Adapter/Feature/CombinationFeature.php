<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Feature;

use Combination;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;

/**
 * This class manages Combination feature.
 */
class CombinationFeature implements FeatureInterface
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
    public function isUsed()
    {
        return Combination::isCurrentlyUsed();
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return Combination::isFeatureActive();
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $this->configuration->set('PS_COMBINATION_FEATURE_ACTIVE', true);
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $this->configuration->set('PS_COMBINATION_FEATURE_ACTIVE', false);
    }

    /**
     * {@inheritdoc}
     */
    public function update($status)
    {
        true === $status ? $this->enable() : $this->disable();
    }
}
