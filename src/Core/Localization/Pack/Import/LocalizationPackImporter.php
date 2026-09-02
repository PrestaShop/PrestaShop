<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

use PrestaShop\PrestaShop\Core\Localization\Pack\Factory\LocalizationPackFactoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Loader\LocalizationPackLoaderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param LocalizationPackLoaderInterface $remoteLocalizationPackLoader
     * @param LocalizationPackLoaderInterface $localLocalizationPackLoader
     * @param LocalizationPackFactoryInterface $localizationPackFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        LocalizationPackLoaderInterface $remoteLocalizationPackLoader,
        LocalizationPackLoaderInterface $localLocalizationPackLoader,
        LocalizationPackFactoryInterface $localizationPackFactory,
        TranslatorInterface $translator
    ) {
        $this->remoteLocalizationPackLoader = $remoteLocalizationPackLoader;
        $this->localLocalizationPackLoader = $localLocalizationPackLoader;
        $this->localizationPackFactory = $localizationPackFactory;
        $this->translator = $translator;
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

        if ($config->shouldDownloadPackData()) {
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
