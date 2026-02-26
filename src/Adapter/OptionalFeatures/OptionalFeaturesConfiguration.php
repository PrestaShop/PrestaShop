<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\OptionalFeatures;

use PrestaShop\PrestaShop\Adapter\Feature\CombinationFeature;
use PrestaShop\PrestaShop\Adapter\Feature\FeatureFeature;
use PrestaShop\PrestaShop\Adapter\Feature\GroupFeature;
use PrestaShop\PrestaShop\Adapter\Discount\Update\DiscountWhenCustomerGroupRemovedUpdater;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class will provide Optional features configuration for a Shop.
 */
class OptionalFeaturesConfiguration implements DataConfigurationInterface
{
    /**
     * @var CombinationFeature
     */
    private $combinationFeature;

    /**
     * @var FeatureFeature
     */
    private $featureFeature;

    /**
     * @var GroupFeature
     */
    private $groupFeature;

    /**
     * @var DiscountWhenCustomerGroupRemovedUpdater
     */
    private $discountWhenCustomerGroupRemovedUpdater;

    public function __construct(
        CombinationFeature $combinationFeature,
        FeatureFeature $featureFeature,
        GroupFeature $groupFeature,
        DiscountWhenCustomerGroupRemovedUpdater $discountWhenCustomerGroupRemovedUpdater
    ) {
        $this->combinationFeature = $combinationFeature;
        $this->featureFeature = $featureFeature;
        $this->groupFeature = $groupFeature;
        $this->discountWhenCustomerGroupRemovedUpdater = $discountWhenCustomerGroupRemovedUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'combinations' => $this->combinationFeature->isActive(),
            'features' => $this->featureFeature->isActive(),
            'customer_groups' => $this->groupFeature->isActive(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->combinationFeature->update((bool) $configuration['combinations']);
            $this->featureFeature->update((bool) $configuration['features']);
            if (!$configuration['customer_groups']) {
                $this->discountWhenCustomerGroupRemovedUpdater->handleCustomerGroupsFeatureDisabled();
            }
            $this->groupFeature->update((bool) $configuration['customer_groups']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['combinations'],
            $configuration['features'],
            $configuration['customer_groups']
        );
    }
}
