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

namespace PrestaShop\PrestaShop\Core\Translation\Builder;

use Exception;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\DTO\DomainTranslation;
use PrestaShop\PrestaShop\Core\Translation\DTO\MessageTranslation;
use PrestaShop\PrestaShop\Core\Translation\DTO\Translations;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Provider\CatalogueProviderFactory;

/**
 * This class provides the catalogue represented as an array.
 * The catalogue is composed by domains, subdomains and messages in each of them.
 * A message has 3 layers : the default wording, the translation extracted from XLF and the one made by the Admin.
 * The representation also includes metadata to summarize number of translations and missing ones for each domain.
 * The catalogue can be extracted for a specific domain and filtered by domain or terms.
 */
class TranslationCatalogueBuilder
{
    public const TYPE_BACK = 'back';
    public const TYPE_FRONT = 'front';
    public const TYPE_MAILS = 'mails';
    public const TYPE_MAILS_BODY = 'mails_body';
    public const TYPE_OTHERS = 'others';
    public const TYPE_MODULES = 'modules';
    public const TYPE_THEMES = 'themes';

    public const ALLOWED_TYPES = [
        self::TYPE_BACK,
        self::TYPE_FRONT,
        self::TYPE_MAILS,
        self::TYPE_MAILS_BODY,
        self::TYPE_OTHERS,
        self::TYPE_MODULES,
        self::TYPE_THEMES,
    ];
    /**
     * @var CatalogueProviderFactory
     */
    private $catalogueProviderFactory;

    public function __construct(CatalogueProviderFactory $catalogueProviderFactory)
    {
        $this->catalogueProviderFactory = $catalogueProviderFactory;
    }

    /**
     * Returns the catalogue as array. This catalogue will contain only the required domain.
     * If search strings are provided, only messages which match them will be returned.
     * Catalogue is the combination of the 3 layers of catalogue : default, file-translated and user-translated.
     * User-translated will override file-translated, which will override default catalogue.
     * Each domain will have counters (number of items and missing translations) as metadata.
     * 'Normalization' will add extra data.
     *
     * @param string $type
     * @param string $locale
     * @param string $domain
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     *
     * @return array
     *
     * @throws Exception
     */
    public function getDomainCatalogue(
        string $type,
        string $locale,
        string $domain,
        array $search,
        ?string $theme,
        ?string $module
    ): array {
        $this->validateParameters($type, $locale, $search, $theme, $module, $domain);

        $domainTranslation = $this->getRawCatalogue(
            $type,
            $locale,
            $search,
            $theme,
            $module,
            $domain
        )->getDomainTranslation($domain);

        if (null === $domainTranslation) {
            $domainTranslation = new DomainTranslation($domain);
        }

        return [
            'info' => [
                'locale' => $locale,
                'domain' => $domain,
                'theme' => $theme,
                'total_translations' => $domainTranslation->getTranslationsCount(),
                'total_missing_translations' => $domainTranslation->getMissingTranslationsCount(),
            ],
            'data' => $domainTranslation->toArray(false),
        ];
    }

    /**
     * Returns the catalogue as array. This catalogue will contain all available domains.
     * If search strings are provided, only messages which match them will be returned.
     * Catalogue is the combination of the 3 layers of catalogue : default, file-translated and user-translated.
     * User-translated will override file-translated, which will override default catalogue.
     * Each domain will have counters (number of items and missing translations) as metadata.
     *
     * @param string $type
     * @param string $locale
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     *
     * @return array
     *
     * @throws Exception
     */
    public function getCatalogue(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module
    ): array {
        return $this->getRawCatalogue(
            $type,
            $locale,
            $search,
            $theme,
            $module
        )->toArray();
    }

    /**
     * This method will return the catalogue as Translations DTO.
     * A translationsDTO contains domainTranslationsDTO which contains MessageTranslationsDTO.
     * Catalogue is the combination of the 3 layers of catalogue : default, file-translated and user-translated.
     * User-translated will override file-translated, which will override default catalogue.
     * Each domain will have counters (number of items and missing translations) as metadata.
     *
     * @param string $type
     * @param string $locale
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     * @param string|null $domain
     *
     * @return Translations
     *
     * @throws UnexpectedTranslationTypeException
     * @throws FileNotFoundException
     */
    public function getRawCatalogue(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module,
        ?string $domain = null
    ): Translations {
        $this->validateParameters($type, $locale, $search, $theme, $module);

        $provider = $this->catalogueProviderFactory->getProvider($type);

        $defaultCatalogue = $provider->getDefaultCatalogue($locale);
        if (null === $domain) {
            $defaultCatalogueMessages = $defaultCatalogue->all();
        } else {
            $defaultCatalogueMessages = [$domain => $defaultCatalogue->all($domain)];
        }
        if (empty($defaultCatalogueMessages)) {
            return new Translations();
        }
        $fileTranslatedCatalogue = $provider->getFileTranslatedCatalogue($locale);
        $userTranslatedCatalogue = $provider->getUserTranslatedCatalogue($locale);

        $translations = new Translations();
        foreach ($defaultCatalogueMessages as $domainName => $messages) {
            $domainTranslation = new DomainTranslation($domainName);

            foreach ($messages as $translationKey => $translationValue) {
                $messageTranslation = new MessageTranslation($translationKey);
                if ($fileTranslatedCatalogue->defines($translationKey, $domainName)) {
                    $messageTranslation->setFileTranslation($fileTranslatedCatalogue->get($translationKey, $domainName));
                }
                if ($userTranslatedCatalogue->defines($translationKey, $domainName)) {
                    $messageTranslation->setUserTranslation($userTranslatedCatalogue->get($translationKey, $domainName));
                }
                // if search is empty or is in catalog default|project|user
                if (empty($search) || $messageTranslation->contains($search)) {
                    $domainTranslation->addMessageTranslation($messageTranslation);
                }
            }

            $translations->addDomainTranslation($domainTranslation);
        }

        return $translations;
    }

    /**
     * @param string $type
     * @param string $locale
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     * @param string|null $domain
     *
     * @throws UnexpectedTranslationTypeException
     */
    private function validateParameters(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module,
        ?string $domain = null
    ): void {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new UnexpectedTranslationTypeException('This \'type\' param is not valid.');
        }
        if (self::TYPE_MODULES === $type && empty($module)) {
            throw new InvalidArgumentException('This \'selected\' param is not valid. Module must be given.');
        }
        if (self::TYPE_THEMES === $type && empty($theme)) {
            throw new InvalidArgumentException('This \'selected\' param is not valid. Theme must be given.');
        }
        if (null !== $domain && empty($domain)) {
            throw new InvalidArgumentException('The given \'domain\' is not valid.');
        }
    }
}
