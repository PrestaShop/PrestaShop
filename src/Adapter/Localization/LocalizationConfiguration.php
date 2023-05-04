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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyManager;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageActivatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LocalizationConfiguration is responsible for 'Improve > International > Localization' page
 * 'Configuration' form data.
 */
class LocalizationConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'default_language',
        'detect_language_from_browser',
        'detect_country_from_browser',
        'default_country',
        'default_currency',
        'timezone',
    ];

    /**
     * @var LanguageActivatorInterface
     */
    private $languageActivator;

    /**
     * @var CurrencyManager
     */
    private $currencyManager;

    /**
     * @param Configuration $configuration
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     * @param LanguageActivatorInterface $languageActivator
     * @param CurrencyManager $currencyManager
     */
    public function __construct(
        Configuration $configuration,
        Context $shopContext,
        FeatureInterface $multistoreFeature,
        LanguageActivatorInterface $languageActivator,
        CurrencyManager $currencyManager
    ) {
        parent::__construct($configuration, $shopContext, $multistoreFeature);

        $this->languageActivator = $languageActivator;
        $this->currencyManager = $currencyManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'default_language' => (int) $this->configuration->get('PS_LANG_DEFAULT', 1, $shopConstraint),
            'detect_language_from_browser' => (bool) $this->configuration->get('PS_DETECT_LANG', false, $shopConstraint),
            'default_country' => (int) $this->configuration->get('PS_COUNTRY_DEFAULT', null, $shopConstraint),
            'detect_country_from_browser' => (bool) $this->configuration->get('PS_DETECT_COUNTRY', false, $shopConstraint),
            'default_currency' => (int) $this->configuration->get('PS_CURRENCY_DEFAULT', null, $shopConstraint),
            'timezone' => $this->configuration->get('PS_TIMEZONE', null, $shopConstraint),
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

            $this->languageActivator->enable((int) $configuration['default_language']);

            // only update currency related data if it has changed
            $currentConfig = $this->getConfiguration();
            if ($currentConfig['default_currency'] != $configuration['default_currency']) {
                $this->updateConfigurationValue('PS_CURRENCY_DEFAULT', 'default_currency', $configuration, $shopConstraint);
                $this->currencyManager->updateDefaultCurrency();
            }

            $this->updateConfigurationValue('PS_LANG_DEFAULT', 'default_language', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_DETECT_LANG', 'detect_language_from_browser', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_COUNTRY_DEFAULT', 'default_country', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_DETECT_COUNTRY', 'detect_country_from_browser', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_TIMEZONE', 'timezone', $configuration, $shopConstraint);
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
            ->setAllowedTypes('default_language', 'int')
            ->setAllowedTypes('detect_language_from_browser', 'bool')
            ->setAllowedTypes('detect_country_from_browser', 'bool')
            ->setAllowedTypes('default_country', 'int')
            ->setAllowedTypes('default_currency', 'int')
            ->setAllowedTypes('timezone', 'string');

        return $resolver;
    }
}
