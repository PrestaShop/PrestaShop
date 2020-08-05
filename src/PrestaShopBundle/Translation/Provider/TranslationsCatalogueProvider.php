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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShopBundle\Translation\Provider\Factory\ProviderFactory;
use PrestaShopBundle\Translation\Provider\Type\TypeInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Retrieves combined and formatted catalogues depending on the provider type.
 */
class TranslationsCatalogueProvider
{
    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    public function __construct(ProviderFactory $providerFactory)
    {
        $this->providerFactory = $providerFactory;
    }

    /**
     * @param TypeInterface $providerType
     * @param string $locale
     * @param string $domain
     * @param array $search
     *
     * @return array
     *
     * @throws FileNotFoundException|NotImplementedException
     */
    public function getDomainCatalogue(
        TypeInterface $providerType,
        string $locale,
        string $domain,
        array $search = []
    ): array {
        $provider = $this->providerFactory->build($providerType);

        $defaultCatalogue = $provider->getDefaultCatalogue($locale);
        if (null === $defaultCatalogue) {
            return [];
        }
        $defaultCatalogue = $this->filterCatalogue($defaultCatalogue, $locale, $domain)->all($domain);

        try {
            $fileTranslatedCatalogue = $provider->getFileTranslatedCatalogue($locale);
            $fileTranslatedCatalogue = (null !== $fileTranslatedCatalogue) ? $this->filterCatalogue($fileTranslatedCatalogue, $locale, $domain)->all($domain) : [];
        } catch (FileNotFoundException $exception) {
            $fileTranslatedCatalogue = [];
        }

        $userTranslatedCatalogue = $provider->getUserTranslatedCatalogue($locale);
        $userTranslatedCatalogue = (null !== $userTranslatedCatalogue) ? $this->filterCatalogue($userTranslatedCatalogue, $locale, $domain)->all($domain) : [];

        $treeDomain = preg_split('/(?=[A-Z])/', $domain, -1, PREG_SPLIT_NO_EMPTY);

        return $this->normalizeCatalogue(
            $defaultCatalogue,
            $fileTranslatedCatalogue,
            $userTranslatedCatalogue,
            $treeDomain,
            $search
        );
    }

    /**
     * @param TypeInterface $providerType
     * @param string $locale
     * @param array $search
     *
     * @return array
     *
     * @throws NotImplementedException
     * @throws FileNotFoundException
     */
    public function getCatalogue(
        TypeInterface $providerType,
        string $locale,
        array $search = []
    ): array {
        $provider = $this->providerFactory->build($providerType);

        $defaultCatalogue = $provider->getDefaultCatalogue($locale);
        $defaultCatalogueMessages = $defaultCatalogue->all();
        if (empty($defaultCatalogueMessages)) {
            return [];
        }
        $fileTranslatedCatalogue = $provider->getFileTranslatedCatalogue($locale);
        $userTranslatedCatalogue = $provider->getUserTranslatedCatalogue($locale);

        $translations = [];

        foreach ($defaultCatalogueMessages as $domain => $messages) {
            $missingTranslations = 0;
            $translations[$domain] = [];

            foreach ($messages as $translationKey => $translationValue) {
                $data = [
                    'default' => $translationKey,
                    'xliff' => $fileTranslatedCatalogue->defines($translationKey, $domain)
                        ? $fileTranslatedCatalogue->get($translationKey, $domain)
                        : null,
                    'database' => $userTranslatedCatalogue->defines($translationKey, $domain)
                        ? $userTranslatedCatalogue->get($translationKey, $domain)
                        : null,
                ];

                // if search is empty or is in catalog default|xliff|database
                if (empty($search) || $this->dataContainsSearchWord($data, $search)) {
                    $translations[$domain][$translationKey] = $data;

                    if (null === $data['xliff'] && null === $data['database']) {
                        ++$missingTranslations;
                    }
                }
            }

            $translations[$domain]['__metadata'] = [
                'count' => count($translations[$domain]),
                'missing_translations' => $missingTranslations,
            ];
        }

        unset($catalogues);

        ksort($translations);

        return $translations;
    }

    /**
     * Filters the catalogue so that only domains matching the filters are kept
     *
     * @param string $locale
     * @param MessageCatalogueInterface $catalogue
     * @param string $domain
     *
     * @return MessageCatalogueInterface
     */
    private function filterCatalogue(MessageCatalogueInterface $catalogue, string $locale, string $domain): MessageCatalogueInterface
    {
        $filteredCatalogue = [];

        foreach ($catalogue->getDomains() as $catalogueDomain) {
            if (preg_match('#^' . preg_quote($domain, '#') . '([A-Za-z]|\.|$)#', $catalogueDomain)) {
                $filteredCatalogue[$catalogueDomain] = $catalogue->all($catalogueDomain);
                break;
            }
        }

        return new MessageCatalogue($locale, $filteredCatalogue);
    }

    /**
     * @param array $defaultCatalogue
     * @param array $fileTranslatedCatalogue
     * @param array $userTranslatedCatalogue
     * @param array $treeDomain
     * @param array $search
     *
     * @return array
     */
    private function normalizeCatalogue(
        array $defaultCatalogue,
        array $fileTranslatedCatalogue,
        array $userTranslatedCatalogue,
        array $treeDomain,
        array $search
    ): array {
        $domainCatalogue = [];
        foreach ($defaultCatalogue as $key => $message) {
            $messageData = [
                'default' => $key,
                'xliff' => (array_key_exists($key, $fileTranslatedCatalogue) ? $fileTranslatedCatalogue[$key] : null),
                'database' => (array_key_exists($key, $userTranslatedCatalogue) ? $userTranslatedCatalogue[$key] : null),
                'tree_domain' => $treeDomain,
            ];
            // if search is empty or is in catalog default|xliff|database
            if (empty($search) || $this->dataContainsSearchWord($messageData, $search)) {
                if (empty($messageData['xliff']) && empty($messageData['database'])) {
                    array_unshift($domainCatalogue, $messageData);
                } else {
                    $domainCatalogue[] = $messageData;
                }
            }
        }

        return $domainCatalogue;
    }

    /**
     * Check if data contains search word.
     *
     * @param array $data
     * @param string|array|null $search
     *
     * @return bool
     */
    private function dataContainsSearchWord(array $data, $search = null): bool
    {
        if (is_string($search)) {
            return $this->elementContainsSearchWord($data, strtolower($search));
        }

        if (is_array($search)) {
            $contains = true;
            foreach ($search as $s) {
                $contains &= $this->elementContainsSearchWord($data, strtolower($s));
            }

            return (bool) $contains;
        }

        return false;
    }

    /**
     * @param array $data
     * @param string $search
     *
     * @return bool
     */
    private function elementContainsSearchWord(array $data, string $search): bool
    {
        return (false !== strpos(strtolower((string) $data['default']), $search)) ||
        (
            (null !== $data['xliff']) &&
            (false !== strpos(strtolower((string) $data['xliff']), $search))
        ) ||
        (
            (null !== $data['database']) &&
            (false !== strpos(strtolower((string) $data['database']), $search))
        );
    }
}
