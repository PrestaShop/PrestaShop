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

declare(strict_types=1);

namespace Tests\Resources;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DummyMultistoreConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'test_conf_1' => (bool) $this->configuration->get('TEST_CONF_1', null, $shopConstraint),
            'test_conf_2' => $this->configuration->get('TEST_CONF_2', null, $shopConstraint),
        ];
    }

    /**
     * @param array $configurationInputValues
     *
     * @return array
     */
    public function updateConfiguration(array $configurationInputValues): array
    {
        if ($this->validateConfiguration($configurationInputValues)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('TEST_CONF_1', 'test_conf_1', $configurationInputValues, $shopConstraint);
            $this->updateConfigurationValue('TEST_CONF_2', 'test_conf_2', $configurationInputValues, $shopConstraint);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    public function buildResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['test_conf_1', 'test_conf_2']);
        $resolver->setAllowedTypes('test_conf_1', 'bool');
        $resolver->setAllowedTypes('test_conf_2', 'string');

        return $resolver;
    }

    // wrapper public method to test the protected "updateConfigurationValue" method in unit tests
    public function dummyUpdateConfigurationValue($fieldName, $inputValues, $shopConstraint)
    {
        $this->updateConfigurationValue('PS_CONF_KEY', $fieldName, $inputValues, $shopConstraint);
    }
}
