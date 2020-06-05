<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;

class TranslationsCatalogueProvider
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string|null
     */
    private $locale;

    /**
     * @var string|null
     */
    private $theme;

    /**
     * @var TranslationCatalogueProviderFactory
     */
    private $translationCatalogueProviderFactory;

    public function __construct(
        TranslationCatalogueProviderFactory $translationCatalogueProviderFactory
    ) {
        $this->translationCatalogueProviderFactory = $translationCatalogueProviderFactory;
    }

    /**
     * @param string $type
     *
     * @return TranslationsCatalogueProvider
     */
    public function setType(string $type): TranslationsCatalogueProvider
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string|null $locale
     *
     * @return TranslationsCatalogueProvider
     */
    public function setLocale(?string $locale): TranslationsCatalogueProvider
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param string|null $theme
     *
     * @return TranslationsCatalogueProvider
     */
    public function setTheme(?string $theme): TranslationsCatalogueProvider
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @param string $domain
     * @param string|array|null $search
     * @param string|null $module
     *
     * @return array
     */
    public function getDomainCatalogue(string $domain, $search = null, ?string $module = null)
    {
        if (null === $this->locale) {
            throw new \LogicException('Locale cannot be null. Call setLocale first');
        }
        if ('Messages' === $domain) {
            $domain = 'messages';
        }

        $provider = $this->translationCatalogueProviderFactory->getDomainCatalogueProvider(
            $this->locale,
            $domain,
            $this->theme,
            $module
        );

        $treeDomain = preg_split('/(?=[A-Z])/', $domain, -1, PREG_SPLIT_NO_EMPTY);

        $defaultCatalogue = $provider->getDefaultCatalogue()->all($domain);
        $fileTranslatedCatalogue = $provider->getFilesystemCatalogue()->all($domain);
        $userTranslatedCatalogue = $provider->getUserTranslatedCatalogue($this->theme)->all($domain);

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
     * @param string|array|null $search
     *
     * @return array
     *
     * @throws FileNotFoundException
     */
    public function getCatalogue($search = null): array
    {
        if (null === $this->type) {
            throw new \LogicException('Translation type cannot be null. Call setType first');
        }
        if (null === $this->locale) {
            throw new \LogicException('Locale cannot be null. Call setLocale first');
        }

        $defaultCatalogue = $this->translationCatalogueProviderFactory->getDefaultCatalogueProvider(
            $this->type,
            $this->locale,
            $this->theme
        )
            ->getDefaultCatalogue();

        $fileTranslatedCatalogue = $this->translationCatalogueProviderFactory->getFileTranslatedCatalogueProvider(
            $this->type,
            $this->locale,
            $this->theme
        )
            ->getFilesystemCatalogue();

        $userTranslatedCatalogue = $this->translationCatalogueProviderFactory->getUserTranslatedCatalogueProvider(
            $this->type,
            $this->locale,
            $this->theme
        )
            ->getUserTranslatedCatalogue($this->theme);

        $translations = [];

        foreach ($defaultCatalogue->all() as $domain => $messages) {
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
