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
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Catalogue;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Domain;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Message;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueProviderFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\BackofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\FrontofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\MailsBodyProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\MailsProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\OthersProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;

/**
 * This class provides the catalogue represented as an array.
 * The catalogue is composed by domains, subdomains and messages in each of them.
 * A message has 3 layers : the default wording, the translation extracted from XLF and the one made by the Admin.
 * The representation also includes metadata to summarize number of translations and missing ones for each domain.
 * The catalogue can be extracted for a specific domain and filtered by domain or terms.
 */
class TranslationCatalogueBuilder
{
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
        )->getDomain($domain);

        if (null === $domainTranslation) {
            $domainTranslation = new Domain($domain);
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
     * @return Catalogue
     *
     * @throws UnexpectedTranslationTypeException
     * @throws TranslationFilesNotFoundException
     */
    public function getRawCatalogue(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module,
        string $domain = null
    ): Catalogue {
        $this->validateParameters($type, $locale, $search, $theme, $module);

        $definition = null;
        switch ($type) {
            case ProviderDefinitionInterface::TYPE_BACK:
                $definition = new BackofficeProviderDefinition();

                break;
            case ProviderDefinitionInterface::TYPE_FRONT:
                $definition = new FrontofficeProviderDefinition();

                break;
            case ProviderDefinitionInterface::TYPE_MAILS_BODY:
                $definition = new MailsBodyProviderDefinition();

                break;
            case ProviderDefinitionInterface::TYPE_MAILS:
                $definition = new MailsProviderDefinition();

                break;
            case ProviderDefinitionInterface::TYPE_OTHERS:
                $definition = new OthersProviderDefinition();

                break;
            case ProviderDefinitionInterface::TYPE_MODULES:
                $definition = new ModuleProviderDefinition($module);

                break;
            case ProviderDefinitionInterface::TYPE_THEMES:
                $definition = new ThemeProviderDefinition($theme);

                break;
        }

        $provider = $this->catalogueProviderFactory->getProvider($definition);

        $defaultCatalogue = $provider->getDefaultCatalogue($locale);
        if (null === $domain) {
            $defaultCatalogueMessages = $defaultCatalogue->all();
        } else {
            $defaultCatalogueMessages = [$domain => $defaultCatalogue->all($domain)];
        }
        if (empty($defaultCatalogueMessages)) {
            return new Catalogue();
        }
        $fileTranslatedCatalogue = $provider->getFileTranslatedCatalogue($locale);
        $userTranslatedCatalogue = $provider->getUserTranslatedCatalogue($locale);

        $catalogue = new Catalogue();
        foreach ($defaultCatalogueMessages as $domainName => $messages) {
            $domainTranslation = new Domain($domainName);

            foreach ($messages as $translationKey => $translationValue) {
                $message = new Message($translationKey);
                if ($fileTranslatedCatalogue->defines($translationKey, $domainName)) {
                    $message->setFileTranslation($fileTranslatedCatalogue->get($translationKey, $domainName));
                }
                if ($userTranslatedCatalogue->defines($translationKey, $domainName)) {
                    $message->setUserTranslation($userTranslatedCatalogue->get($translationKey, $domainName));
                }
                // if search is empty or is in catalog default|project|user
                if (empty($search) || $message->contains($search)) {
                    $domainTranslation->addMessage($message);
                }
            }

            $catalogue->addDomain($domainTranslation);
        }

        return $catalogue;
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
        string $domain = null
    ): void {
        if (!in_array($type, ProviderDefinitionInterface::ALLOWED_TYPES)) {
            throw new UnexpectedTranslationTypeException('This \'type\' param is not valid.');
        }
        if (ProviderDefinitionInterface::TYPE_MODULES === $type && empty($module)) {
            throw new InvalidArgumentException('This \'selected\' param is not valid. Module must be given.');
        }
        if (ProviderDefinitionInterface::TYPE_THEMES === $type && empty($theme)) {
            throw new InvalidArgumentException('This \'selected\' param is not valid. Theme must be given.');
        }
        if (null !== $domain && empty($domain)) {
            throw new InvalidArgumentException('The given \'domain\' is not valid.');
        }
    }
}
