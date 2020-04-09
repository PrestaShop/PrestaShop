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

namespace PrestaShopBundle\Translation\Factory;

use PrestaShopBundle\Translation\Provider\ProviderInterface;
use PrestaShopBundle\Translation\View\TreeBuilder;

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
    public function createCatalogue($domainIdentifier, $locale = self::DEFAULT_LOCALE)
    {
        foreach ($this->providers as $provider) {
            if ($domainIdentifier === $provider->getIdentifier()) {
                return $provider->setLocale($locale)->getMessageCatalogue();
            }
        }

        throw new ProviderNotFoundException($domainIdentifier);
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslationsArray(
        $domainIdentifier,
        $locale = self::DEFAULT_LOCALE,
        $theme = null,
        $search = null
    ) {
        foreach ($this->providers as $provider) {
            if ($domainIdentifier === $provider->getIdentifier()) {
                return $this->makeTranslationArray($provider, $locale, $theme, $search);
            }
        }

        throw new ProviderNotFoundException($domainIdentifier);
    }

    /**
     * @param ProviderInterface $provider
     *
     * @return static
     */
    public function addProvider(ProviderInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @param ProviderInterface[] $providers
     *
     * @return static
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }

        return $this;
    }

    /**
     * @param ProviderInterface $provider
     * @param string $locale
     * @param string|null $theme
     * @param string|null $search
     *
     * @return array
     */
    private function makeTranslationArray(ProviderInterface $provider, $locale, $theme, $search = null)
    {
        $provider->setLocale($locale);

        $defaultCatalogue = $provider->getDefaultCatalogue();
        $xliffCatalogue = $provider->getXliffCatalogue();
        $databaseCatalogue = $provider->getDatabaseCatalogue($theme);

        $translations = [];

        foreach ($defaultCatalogue->all() as $domain => $messages) {
            $missingTranslations = 0;

            foreach ($messages as $translationKey => $translationValue) {
                $data = array(
                    'default' => $translationKey,
                    'xlf' => $xliffCatalogue->defines($translationKey, $domain)
                        ? $xliffCatalogue->get($translationKey, $domain)
                        : null,
                    'db' => $databaseCatalogue->defines($translationKey, $domain)
                        ? $databaseCatalogue->get($translationKey, $domain)
                        : null,
                );

                // if search is empty or is in catalog default|xlf|database
                if (empty($search) || $this->dataContainsSearchWord($search, $data)) {
                    $translations[$domain][$translationKey] = $data;

                    if (null === $data['xlf'] && null === $data['db']) {
                        ++$missingTranslations;
                    }
                }
            }

            $translations[$domain]['__metadata'] = array(
                'count' => count($translations[$domain]),
                'missing_translations' => $missingTranslations
            );
        }

        ksort($translations);

        return $translations;
    }

    /**
     * Check if data contains search word.
     *
     * @param $search
     * @param $data
     *
     * @return bool
     */
    private function dataContainsSearchWord($search, $data)
    {
        if (is_string($search)) {
            $search = strtolower($search);

            return false !== strpos(strtolower($data['default']), $search) ||
                false !== strpos(strtolower($data['xlf']), $search) ||
                false !== strpos(strtolower($data['db']), $search);
        }

        if (is_array($search)) {
            $contains = true;
            foreach ($search as $s) {
                $s = strtolower($s);
                $contains &= false !== strpos(strtolower($data['default']), $s) ||
                    false !== strpos(strtolower($data['xlf']), $s) ||
                    false !== strpos(strtolower($data['db']), $s);
            }

            return $contains;
        }

        return false;
    }
}
