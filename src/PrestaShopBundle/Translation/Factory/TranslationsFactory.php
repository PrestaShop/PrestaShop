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

namespace PrestaShopBundle\Translation\Factory;

use PrestaShopBundle\Translation\Provider\ProviderInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * This class returns a collection of translations, using a locale and an identifier.
 */
class TranslationsFactory implements TranslationsFactoryInterface
{
    /**
     * @var ProviderInterface[] the list of translation providers
     */
    private $providers = [];

    /**
     * {@inheritdoc}
     */
    public function createCatalogue(string $domainIdentifier, string $locale = self::DEFAULT_LOCALE): MessageCatalogueInterface
    {
        $provider = $this->getProviderByIdentifier($domainIdentifier);

        return $provider->setLocale($locale)->getMessageCatalogue();
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslationsArray(
        string $domainIdentifier,
        string $locale = self::DEFAULT_LOCALE,
        ?string $theme = null,
        ?string $search = null
    ): array {
        $provider = $this->getProviderByIdentifier($domainIdentifier);

        return $this->makeTranslationArray($provider, $locale, $theme, $search);
    }

    /**
     * @param ProviderInterface $provider
     *
     * @return static
     */
    public function addProvider(ProviderInterface $provider): self
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @param ProviderInterface[] $providers
     *
     * @return static
     */
    public function setProviders(array $providers): self
    {
        $this->providers = [];
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return ProviderInterface
     *
     * @throws ProviderNotFoundException
     */
    private function getProviderByIdentifier(string $identifier): ProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($identifier === $provider->getIdentifier()) {
                return $provider;
            }
        }

        throw new ProviderNotFoundException($identifier);
    }

    /**
     * @param ProviderInterface $provider
     * @param string $locale
     * @param string|null $theme
     * @param string|null $search
     *
     * @return array
     */
    private function makeTranslationArray(
        ProviderInterface $provider,
        string $locale,
        ?string $theme,
        ?string $search = null
    ): array {
        $provider->setLocale($locale);

        $defaultCatalogue = $provider->getDefaultCatalogue();
        $xliffCatalogue = $provider->getFilesystemCatalogue();
        $databaseCatalogue = $provider->getUserTranslatedCatalogue();

        $translations = [];

        foreach ($defaultCatalogue->all() as $domain => $messages) {
            $missingTranslations = 0;

            foreach ($messages as $translationKey => $translationValue) {
                $data = [
                    'default' => $translationKey,
                    'xlf' => $xliffCatalogue->defines($translationKey, $domain)
                        ? $xliffCatalogue->get($translationKey, $domain)
                        : null,
                    'db' => $databaseCatalogue->defines($translationKey, $domain)
                        ? $databaseCatalogue->get($translationKey, $domain)
                        : null,
                ];

                // if search is empty or is in catalog default|xlf|database
                if (empty($search) || $this->dataContainsSearchWord($search, $data)) {
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
     * Check if data contains search word.
     *
     * @param string|null $search
     * @param array $data
     *
     * @return bool
     */
    private function dataContainsSearchWord(?string $search, array $data): bool
    {
        if (is_string($search)) {
            $search = strtolower($search);

            return false !== strpos(strtolower($data['default']), $search) ||
                false !== strpos(strtolower($data['xliff']), $search) ||
                false !== strpos(strtolower($data['database']), $search);
        }

        if (is_array($search)) {
            $contains = true;
            foreach ($search as $s) {
                $s = strtolower($s);
                $contains &= false !== strpos(strtolower($data['default']), $s) ||
                    false !== strpos(strtolower($data['xliff']), $s) ||
                    false !== strpos(strtolower($data['database']), $s);
            }

            return $contains;
        }

        return false;
    }
}
