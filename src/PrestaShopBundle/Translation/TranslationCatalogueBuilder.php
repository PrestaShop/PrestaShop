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

namespace PrestaShopBundle\Translation;

use Exception;
use PrestaShopBundle\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShopBundle\Translation\Provider\CatalogueProviderFactory;
use PrestaShopBundle\Translation\View\TranslationsTreeBuilder;

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
    /**
     * @var TranslationsTreeBuilder
     */
    private $translationsTreeBuilder;

    public function __construct(
        CatalogueProviderFactory $catalogueProviderFactory,
        TranslationsTreeBuilder $translationsTreeBuilder
    ) {
        $this->catalogueProviderFactory = $catalogueProviderFactory;
        $this->translationsTreeBuilder = $translationsTreeBuilder;
    }

    /**
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
        $this->validateParameters($type, $locale, $search, $theme, $module);

        $provider = $this->catalogueProviderFactory->getProvider($type);

        $defaultCatalogue = $provider->getDefaultCatalogue($locale)->all($domain);
        $fileTranslatedCatalogue = $provider->getFileTranslatedCatalogue($locale)->all($domain);
        $userTranslatedCatalogue = $provider->getUserTranslatedCatalogue($locale)->all($domain);

        return $this->normalizeCatalogue(
            $defaultCatalogue,
            $fileTranslatedCatalogue,
            $userTranslatedCatalogue,
            $locale,
            $domain,
            $search,
            $theme
        );
    }

    /**
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
        $this->validateParameters($type, $locale, $search, $theme, $module);

        $provider = $this->catalogueProviderFactory->getProvider($type);

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

        ksort($translations);

        return $translations;
    }

    /**
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
    public function getTree(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module
    ): array {
        $catalogue = $this->getCatalogue($type, $locale, $search, $theme, $module);

        return $this->translationsTreeBuilder->buildDomainTreeForApi(
            $catalogue,
            $locale,
            $search,
            $theme,
            $module
        );
    }

    /**
     * @param string $type
     * @param string $locale
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     *
     * @throws Exception
     */
    private function validateParameters(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module
    ): void {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new UnexpectedTranslationTypeException('This \'type\' param is not valid.');
        }
        if (self::TYPE_MODULES === $type && empty($module)) {
            throw new Exception('This \'selected\' param is not valid. Module must be given.');
        }
        if (self::TYPE_THEMES === $type && empty($theme)) {
            throw new Exception('This \'selected\' param is not valid. Theme must be given.');
        }
    }

    /**
     * @param array $defaultCatalogue
     * @param array $fileTranslatedCatalogue
     * @param array $userTranslatedCatalogue
     * @param string $locale
     * @param string $domain
     * @param array $search
     * @param string $theme
     *
     * @return array[]
     */
    private function normalizeCatalogue(
        array $defaultCatalogue,
        array $fileTranslatedCatalogue,
        array $userTranslatedCatalogue,
        string $locale,
        string $domain,
        array $search,
        string $theme
    ): array {
        $treeDomain = preg_split('/(?=[A-Z])/', $domain, -1, PREG_SPLIT_NO_EMPTY);

        $normalizedCatalogue = [
            'data' => [],
        ];

        foreach ($defaultCatalogue as $key => $message) {
            $data = [
                'default' => $key,
                'xliff' => (array_key_exists($key, (array) $fileTranslatedCatalogue) ? $fileTranslatedCatalogue[$key] : null),
                'database' => (array_key_exists($key, (array) $userTranslatedCatalogue) ? $userTranslatedCatalogue[$key] : null),
                'tree_domain' => $treeDomain,
            ];
            // if search is empty or is in catalog default|xlf|database
            if (empty($search) || $this->dataContainsSearchWord($search, $data)) {
                if (empty($data['xliff']) && empty($data['database'])) {
                    // The missing translations are placed on top
                    array_unshift($normalizedCatalogue['data'], $data);
                } else {
                    $normalizedCatalogue['data'][] = $data;
                }
            }
        }

        // Count missing translations
        $missingTranslations = 0;
        foreach ($normalizedCatalogue['data'] as $message) {
            if (empty($message['xliff']) && empty($message['database'])) {
                ++$missingTranslations;
            }
        }

        $normalizedCatalogue['info'] = [
            'locale' => $locale,
            'domain' => $domain,
            'theme' => $theme,
            'total_translations' => count($normalizedCatalogue['data']),
            'total_missing_translations' => $missingTranslations,
        ];

        return $normalizedCatalogue;
    }

    /**
     * Check if data contains search word.
     *
     * @param array $search
     * @param array $data
     *
     * @return bool
     */
    private function dataContainsSearchWord(array $search, array $data): bool
    {
        $contains = true;
        foreach ($search as $s) {
            $s = strtolower($s);
            $contains &= false !== strpos(strtolower($data['default']), $s)
                || (null !== $data['xliff'] && false !== strpos(strtolower($data['xliff']), $s))
                || (null !== $data['database'] && false !== strpos(strtolower($data['database']), $s));
        }

        return (bool) $contains;
    }
}
