<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\CreditSlip;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;

/**
 * Responsible for saving configuration options for credit slip
 */
final class CreditSlipOptionsConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'slip_prefix' => $this->configuration->get('PS_CREDIT_SLIP_PREFIX'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            if (
                array_key_exists('multistore_slip_prefix', $configuration)
                && $configuration['multistore_slip_prefix'] === false) {
                unset($configuration['multistore_slip_prefix']);
            }

            $this->updateConfigurationValue(
                'PS_CREDIT_SLIP_PREFIX',
                'slip_prefix',
                $configuration,
                $this->getShopConstraint()
            );
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset($configuration['slip_prefix']);
    }
}
