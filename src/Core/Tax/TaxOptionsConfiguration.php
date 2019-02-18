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

namespace PrestaShop\PrestaShop\Core\Tax;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Handles configuration data for tax options.
 */
final class TaxOptionsConfiguration implements DataConfigurationInterface
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
            'enable_tax' => $this->configuration->get('PS_TAX'),
            'display_tax_in_cart' => $this->configuration->get('PS_TAX_DISPLAY'),
            'address_type' => $this->configuration->get('PS_TAX_ADDRESS_TYPE'),
            'use_eco_tax' => $this->configuration->get('PS_USE_ECOTAX'),
            'eco_tax_rule_group' => $this->configuration->get('PS_ECOTAX_TAX_RULES_GROUP_ID'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_TAX', (int) $configuration['enable_tax']);
            $this->configuration->set('PS_TAX_DISPLAY', (int) $configuration['display_tax_in_cart']);
            $this->configuration->set('PS_TAX_ADDRESS_TYPE', $configuration['address_type']);
            $this->configuration->set('PS_USE_ECOTAX', (int) $configuration['use_eco_tax']);

            if ($this->configuration->get('PS_USE_ECOTAX')) {
                $this->configuration->set('PS_ECOTAX_TAX_RULES_GROUP_ID', $configuration['eco_tax_rule_group']);
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['enable_tax'],
            $configuration['display_tax_in_cart'],
            $configuration['address_type'],
            $configuration['use_eco_tax'],
            $configuration['eco_tax_rule_group']
        );
    }
}
