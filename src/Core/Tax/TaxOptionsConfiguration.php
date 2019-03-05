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
use PrestaShop\PrestaShop\Core\Tax\Ecotax\ProductEcotaxResetterInterface;

/**
 * Handles configuration data for tax options.
 */
final class TaxOptionsConfiguration implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;
    /**
     * @var ProductEcotaxResetterInterface
     */
    private $productEcotaxResetter;

    /**
     * @param ConfigurationInterface $configuration
     * @param ProductEcotaxResetterInterface $productEcotaxResetter
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ProductEcotaxResetterInterface $productEcotaxResetter
    ) {
        $this->configuration = $configuration;
        $this->productEcotaxResetter = $productEcotaxResetter;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'enable_tax' => (bool) $this->configuration->get('PS_TAX'),
            'display_tax_in_cart' => (bool) $this->configuration->get('PS_TAX_DISPLAY'),
            'tax_address_type' => $this->configuration->get('PS_TAX_ADDRESS_TYPE'),
            'use_eco_tax' => (bool) $this->configuration->get('PS_USE_ECOTAX'),
            'eco_tax_rule_group' => $this->configuration->get('PS_ECOTAX_TAX_RULES_GROUP_ID'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_TAX', (bool) $configuration['enable_tax']);
            $this->configuration->set('PS_TAX_DISPLAY', (bool) $configuration['display_tax_in_cart']);
            $this->configuration->set('PS_TAX_ADDRESS_TYPE', $configuration['tax_address_type']);
            $this->updateEcotax($configuration['use_eco_tax']);

            if ($configuration['use_eco_tax'] && isset($configuration['eco_tax_rule_group'])) {
                $this->configuration->set('PS_ECOTAX_TAX_RULES_GROUP_ID', $configuration['eco_tax_rule_group']);
            }

            if (false === $configuration['enable_tax']) {
                $this->configuration->set('PS_TAX_DISPLAY', false);
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
            $configuration['tax_address_type'],
            $configuration['use_eco_tax']
        );
    }

    /**
     * Responsible for ecotax update
     *
     * @param bool $isEnabled
     */
    private function updateEcotax($isEnabled)
    {
        $wasEnabled = (bool) $this->configuration->get('PS_USE_ECOTAX');

        if (!$isEnabled && $wasEnabled !== $isEnabled) {
            $this->productEcotaxResetter->reset();
        }
        $this->configuration->set('PS_USE_ECOTAX', $isEnabled);
    }
}
