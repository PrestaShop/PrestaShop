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
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageActivatorInterface;

/**
 * Class LocalizationConfiguration is responsible for 'Improve > International > Localization' page
 * 'Configuration' form data.
 */
class LocalizationConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LanguageActivatorInterface
     */
    private $languageActivator;

    /**
     * @var CurrencyManager
     */
    private $currencyManager;

    /**
     * @var AdminModuleDataProvider
     */
    private $adminModuleDataProvider;

    /**
     * @param Configuration $configuration
     * @param LanguageActivatorInterface $languageActivator
     * @param CurrencyManager $currencyManager
     * @param AdminModuleDataProvider $adminModuleDataProvider
     */
    public function __construct(
        Configuration $configuration,
        LanguageActivatorInterface $languageActivator,
        CurrencyManager $currencyManager,
        AdminModuleDataProvider $adminModuleDataProvider
    ) {
        $this->configuration = $configuration;
        $this->languageActivator = $languageActivator;
        $this->currencyManager = $currencyManager;
        $this->adminModuleDataProvider = $adminModuleDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'default_language' => $this->configuration->getInt('PS_LANG_DEFAULT'),
            'detect_language_from_browser' => $this->configuration->getBoolean('PS_DETECT_LANG'),
            'default_country' => $this->configuration->getInt('PS_COUNTRY_DEFAULT'),
            'detect_country_from_browser' => $this->configuration->getBoolean('PS_DETECT_COUNTRY'),
            'default_currency' => $this->configuration->getInt('PS_CURRENCY_DEFAULT'),
            'timezone' => $this->configuration->get('PS_TIMEZONE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->languageActivator->enable((int) $config['default_language']);

            // only update currency related data if it has changed
            $currentConfig = $this->getConfiguration();
            if ($currentConfig['default_currency'] != $config['default_currency']) {
                $this->configuration->set('PS_CURRENCY_DEFAULT', (int) $config['default_currency']);
                $this->currencyManager->updateDefaultCurrency();
            }

            // remove module list cache if the default country changed
            if ($currentConfig['default_country'] != $config['default_country']) {
                $this->adminModuleDataProvider->clearModuleListCache();
            }

            $this->configuration->set('PS_LANG_DEFAULT', (int) $config['default_language']);
            $this->configuration->set('PS_DETECT_LANG', (int) $config['detect_language_from_browser']);
            $this->configuration->set('PS_COUNTRY_DEFAULT', (int) $config['default_country']);
            $this->configuration->set('PS_DETECT_COUNTRY', (int) $config['detect_country_from_browser']);
            $this->configuration->set('PS_TIMEZONE', $config['timezone']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['default_language'],
            $config['detect_language_from_browser'],
            $config['default_country'],
            $config['detect_country_from_browser'],
            $config['default_currency'],
            $config['timezone']
        );
    }
}
