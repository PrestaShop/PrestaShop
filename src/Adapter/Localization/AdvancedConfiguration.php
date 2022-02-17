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

namespace PrestaShop\PrestaShop\Adapter\Localization;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdvancedConfiguration is responsible for 'Improve > International > Localization' page
 * 'Advanced' form data.
 */
class AdvancedConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['language_identifier', 'country_identifier'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'language_identifier' => $this->configuration->get('PS_LOCALE_LANGUAGE', null, $shopConstraint),
            'country_identifier' => $this->configuration->get('PS_LOCALE_COUNTRY', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_LOCALE_LANGUAGE', 'language_identifier', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_LOCALE_COUNTRY', 'country_identifier', $configuration, $shopConstraint);
        }

        return $errors;
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('language_identifier', 'string')
            ->setAllowedTypes('country_identifier', 'string')
            ->setAllowedTypes('volume_unit', 'string')
            ->setAllowedTypes('dimension_unit', 'string');

        return $resolver;
    }
}
