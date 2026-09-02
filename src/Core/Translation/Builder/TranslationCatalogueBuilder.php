<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Builder;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Catalogue;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Domain;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Message;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueProviderFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\OthersProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use PrestaShop\PrestaShop\Core\Util\Inflector;
use PrestaShopBundle\Translation\Provider\AbstractProvider;

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
     * @param ProviderDefinitionInterface $providerDefinition Translation storage provider configuration
     * @param string $locale
     * @param string $domain
     * @param array $search
     *
     * @return array
     *
     * @throws TranslationFilesNotFoundException
     * @throws UnexpectedTranslationTypeException
     */
    public function getDomainCatalogue(
        ProviderDefinitionInterface $providerDefinition,
        string $locale,
        string $domain,
        array $search
    ): array {
        $this->validateParameters($providerDefinition, $domain);

        // When building tree, we keep 3 leaves of Domain i.e OneTwoThreeFour will become OneTwoThree_four
        // see PrestaShop\PrestaShop\Core\Translation\Builder\Map\Domain::mergeTree
        // When getting messages for a domain, we have to do the reverse operation to match the catalogue domain
        $catalogueDomain = $domain;
        if ($catalogueDomain !== OthersProviderDefinition::OTHERS_DOMAIN_NAME) {
            $catalogueDomain = ucfirst(Inflector::getInflector()->camelize($catalogueDomain));
        }

        $domainTranslation = $this->getRawCatalogue(
            $providerDefinition,
            $locale,
            $search,
            $catalogueDomain
        )->getDomain($catalogueDomain);

        if (null === $domainTranslation) {
            $domainTranslation = new Domain($catalogueDomain);
        }

        return [
            'info' => [
                'locale' => $locale,
                'domain' => $domain,
                'theme' => $providerDefinition instanceof ThemeProviderDefinition ? $providerDefinition->getThemeName() : null,
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
     * @param ProviderDefinitionInterface $providerDefinition Translation storage provider configuration
     * @param string $locale
     * @param array $search
     *
     * @return array
     *
     * @throws TranslationFilesNotFoundException
     * @throws UnexpectedTranslationTypeException
     */
    public function getCatalogue(
        ProviderDefinitionInterface $providerDefinition,
        string $locale,
        array $search
    ): array {
        return $this->getRawCatalogue(
            $providerDefinition,
            $locale,
            $search
        )->toArray();
    }

    /**
     * This method will return the catalogue as Translations DTO.
     * A translationsDTO contains domainTranslationsDTO which contains MessageTranslationsDTO.
     * Catalogue is the combination of the 3 layers of catalogue : default, file-translated and user-translated.
     * User-translated will override file-translated, which will override default catalogue.
     * Each domain will have counters (number of items and missing translations) as metadata.
     *
     * @param ProviderDefinitionInterface $providerDefinition Translation storage provider configuration
     * @param string $locale
     * @param array $search
     * @param string|null $domain
     *
     * @return Catalogue
     *
     * @throws TranslationFilesNotFoundException
     * @throws UnexpectedTranslationTypeException
     */
    public function getRawCatalogue(
        ProviderDefinitionInterface $providerDefinition,
        string $locale,
        array $search,
        ?string $domain = null
    ): Catalogue {
        $this->validateParameters($providerDefinition);

        $provider = $this->catalogueProviderFactory->getProvider($providerDefinition);

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
            $domainName = (string) $domainName;
            $domainTranslation = new Domain($domainName);

            foreach ($messages as $translationKey => $translationValue) {
                $translationKey = (string) $translationKey;
                $message = new Message($translationKey);
                if ($locale === AbstractProvider::DEFAULT_LOCALE) {
                    $message->setFileTranslation($translationKey);
                }
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
     * @param ProviderDefinitionInterface $providerDefinition Translation storage provider configuration
     * @param string|null $domain
     *
     * @throws UnexpectedTranslationTypeException
     */
    private function validateParameters(
        ProviderDefinitionInterface $providerDefinition,
        ?string $domain = null
    ): void {
        if (!in_array($providerDefinition->getType(), ProviderDefinitionInterface::ALLOWED_TYPES)) {
            throw new UnexpectedTranslationTypeException('This \'type\' param is not valid.');
        }
        if (null !== $domain && empty($domain)) {
            throw new InvalidArgumentException('The given \'domain\' is not valid.');
        }
    }
}
