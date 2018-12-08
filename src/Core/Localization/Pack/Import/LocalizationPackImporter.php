<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Factory\LocalizationPackFactoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Loader\LocalizationPackLoaderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LocalizationPackImporter is responsible for importing localization pack.
 */
final class LocalizationPackImporter implements LocalizationPackImporterInterface
{
    /**
     * @var LocalizationPackLoaderInterface
     */
    private $remoteLocalizationPackLoader;

    /**
     * @var LocalizationPackLoaderInterface
     */
    private $localLocalizationPackLoader;

    /**
     * @var LocalizationPackFactoryInterface
     */
    private $localizationPackFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param LocalizationPackLoaderInterface $remoteLocalizationPackLoader
     * @param LocalizationPackLoaderInterface $localLocalizationPackLoader
     * @param LocalizationPackFactoryInterface $localizationPackFactory
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        LocalizationPackLoaderInterface $remoteLocalizationPackLoader,
        LocalizationPackLoaderInterface $localLocalizationPackLoader,
        LocalizationPackFactoryInterface $localizationPackFactory,
        TranslatorInterface $translator,
        ConfigurationInterface $configuration
    ) {
        $this->remoteLocalizationPackLoader = $remoteLocalizationPackLoader;
        $this->localLocalizationPackLoader = $localLocalizationPackLoader;
        $this->localizationPackFactory = $localizationPackFactory;
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function import(LocalizationPackImportConfig $config)
    {
        $errors = $this->checkConfig($config);
        if (!empty($errors)) {
            return $errors;
        }

        $pack = null;

        if ($config->shouldDownloadPackData() || $this->configuration->get('_PS_HOST_MODE_')) {
            $pack = $this->remoteLocalizationPackLoader->getLocalizationPack(
                $config->getCountryIsoCode()
            );
        }

        if (null === $pack) {
            $pack = $this->localLocalizationPackLoader->getLocalizationPack(
                $config->getCountryIsoCode()
            );

            if (null === $pack) {
                $error = $this->trans('Cannot load the localization pack.', 'Admin.International.Notification');

                return [$error];
            }
        }

        $localizationPack = $this->localizationPackFactory->createNew();

        $localizationPack->loadLocalisationPack(
            $pack,
            $config->getContentToImport(),
            false,
            $config->getCountryIsoCode()
        );

        return $localizationPack->getErrors();
    }

    /**
     * Check if configuration is valid.
     *
     * @param LocalizationPackImportConfig $config
     *
     * @return array Errors if any
     */
    private function checkConfig(LocalizationPackImportConfig $config)
    {
        if (empty($config->getCountryIsoCode())) {
            $error = $this->trans('Invalid selection', 'Admin.Notifications.Error');

            return [$error];
        }

        if (empty($config->getContentToImport())) {
            $error = $this->trans('Please select at least one item to import.', 'Admin.International.Notification');

            return [$error];
        }

        $contentItems = [
            LocalizationPackImportConfigInterface::CONTENT_STATES,
            LocalizationPackImportConfigInterface::CONTENT_TAXES,
            LocalizationPackImportConfigInterface::CONTENT_CURRENCIES,
            LocalizationPackImportConfigInterface::CONTENT_LANGUAGES,
            LocalizationPackImportConfigInterface::CONTENT_UNITS,
            LocalizationPackImportConfigInterface::CONTENT_GROUPS,
        ];

        foreach ($config->getContentToImport() as $contentItem) {
            if (!in_array($contentItem, $contentItems)) {
                $error = $this->trans('Invalid selection', 'Admin.Notifications.Error');

                return [$error];
            }
        }

        return [];
    }

    /**
     * Translate message.
     *
     * @param string $message
     * @param string $domain
     * @param array $params
     *
     * @return string
     */
    private function trans($message, $domain, array $params = [])
    {
        return $this->translator->trans($message, $params, $domain);
    }
}
