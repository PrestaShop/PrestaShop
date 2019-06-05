<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Localization;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Class LocalUnitsConfiguration is responsible for 'Improve > International > Localization' page
 * 'Local units' form data.
 */
class LocalUnitsConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
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
            'weight_unit' => $this->configuration->get('PS_WEIGHT_UNIT'),
            'distance_unit' => $this->configuration->get('PS_DISTANCE_UNIT'),
            'volume_unit' => $this->configuration->get('PS_VOLUME_UNIT'),
            'dimension_unit' => $this->configuration->get('PS_DIMENSION_UNIT'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_WEIGHT_UNIT', $config['weight_unit']);
            $this->configuration->set('PS_DISTANCE_UNIT', $config['distance_unit']);
            $this->configuration->set('PS_VOLUME_UNIT', $config['volume_unit']);
            $this->configuration->set('PS_DIMENSION_UNIT', $config['dimension_unit']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['weight_unit'],
            $config['distance_unit'],
            $config['volume_unit'],
            $config['dimension_unit']
        );
    }
}
