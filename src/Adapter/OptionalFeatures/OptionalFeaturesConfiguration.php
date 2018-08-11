<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\OptionalFeatures;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Feature\CombinationFeature;
use PrestaShop\PrestaShop\Adapter\Feature\FeatureFeature;
use PrestaShop\PrestaShop\Adapter\Feature\GroupFeature;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class will provide Optional features configuration for a Shop
 */
class OptionalFeaturesConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

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

    public function __construct(
        Configuration $configuration,
        CombinationFeature $combinationFeature,
        FeatureFeature $featureFeature,
        GroupFeature $groupFeature
    )
    {
        $this->configuration = $configuration;
        $this->combinationFeature = $combinationFeature;
        $this->featureFeature = $featureFeature;
        $this->groupFeature = $groupFeature;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return array(
            'combinations' => $this->combinationFeature->isActive(),
            'features' => $this->featureFeature->isActive(),
            'customer_groups' => $this->groupFeature->isActive(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->combinationFeature->update((bool) $configuration['combinations']);
            $this->featureFeature->update((bool) $configuration['features']);
            $this->groupFeature->update((bool) $configuration['customer_groups']);
        }

        return array();
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
