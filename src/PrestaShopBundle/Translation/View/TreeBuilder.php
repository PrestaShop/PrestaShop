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

namespace PrestaShopBundle\Translation\View;

use PrestaShop\PrestaShop\Core\Util\Inflector;
use PrestaShopBundle\Translation\Provider\AbstractProvider;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class TreeBuilder
{
    /**
     * @var string
     */
    private $locale;
    /**
     * @var string|null
     */
    private $theme;

    /**
     * @param string $locale
     * @param string|null $theme
     */
    public function __construct($locale, $theme)
    {
        $this->locale = $locale;
        $this->theme = $theme;
    }

    /**
     * @param AbstractProvider $provider
     * @param string|array|null $search
     *
     * @return array|mixed
     */
    public function makeTranslationArray(AbstractProvider $provider, $search = null)
    {
        $provider->setLocale($this->locale);

        if ('theme' === $provider->getIdentifier()) {
            $defaultCatalogue = $provider->getMessageCatalogue();
        } else {
            $defaultCatalogue = $provider->getDefaultCatalogue();
        }

        $xliffCatalogue = $provider->getXliffCatalogue();
        $databaseCatalogue = $provider->getDatabaseCatalogue($this->theme);

        $translations = [];

        foreach ($defaultCatalogue->all() as $domain => $messages) {
            $missingTranslations = 0;

            foreach ($messages as $translationKey => $translationValue) {
                $data = [
                    'xlf' => $xliffCatalogue->defines($translationKey, $domain)
                        ? $xliffCatalogue->get($translationKey, $domain)
                        : null,
                    'db' => $databaseCatalogue->defines($translationKey, $domain)
                        ? $databaseCatalogue->get($translationKey, $domain)
                        : null,
                ];

                // if search is empty or is in catalog default|xlf|database
                if (empty($search) || $this->dataContainsSearchWord($search, array_merge(['default' => $translationKey], $data))) {
                    $translations[$domain][$translationKey] = $data;

                    if (empty($data['xlf'])
                        && empty($data['db'])
                    ) {
                        ++$missingTranslations;
                    }
                }
            }

            $translations[$domain]['__metadata'] = ['missing_translations' => $missingTranslations];
        }

        ksort($translations);

        return $translations;
    }

    /**
     * Check if data contains search word.
     *
     * @param string|array|null $search
     * @param array $data
     *
     * @return bool
     */
    private function dataContainsSearchWord($search, $data)
    {
        if (is_string($search)) {
            $search = strtolower($search);

            return str_contains(strtolower($data['default']), $search) ||
                str_contains(strtolower($data['xlf']), $search) ||
                str_contains(strtolower($data['db']), $search);
        }

        if (is_array($search)) {
            $contains = true;
            foreach ($search as $s) {
                $s = strtolower($s);
                $contains &= str_contains(strtolower($data['default']), $s) ||
                    str_contains(strtolower($data['xlf']), $s) ||
                    str_contains(strtolower($data['db']), $s);
            }

            return $contains;
        }

        return false;
    }

    /**
     * @return array
     */
    public function makeTranslationsTree($catalogue)
    {
        $translationsTree = [];

        foreach ($catalogue as $domain => $messages) {
            $tableisedDomain = Inflector::getInflector()->tableize($domain);
            // the third component of the domain may have underscores, so we need to limit pieces to 3
            $parts = explode('_', $tableisedDomain, 3);
            /** @var array $subtree */
            $subtree = &$translationsTree;

            foreach ($parts as $part) {
                $subdomain = ucfirst($part);

                if (!array_key_exists($subdomain, $subtree)) {
                    $subtree[$subdomain] = [];
                }

                $subtree = &$subtree[$subdomain];
            }

            $subtree['__messages'] = [$domain => $messages];
            if (isset($messages['__metadata'])) {
                $subtree['__fixed_length_id'] = '_' . sha1($domain);
                [$subtree['__domain']] = explode('.', $domain);
                $subtree['__metadata'] = $messages['__metadata'];
                $subtree['__metadata']['domain'] = $subtree['__domain'];
                unset($messages['__metadata']);
            }
        }

        return $translationsTree;
    }

    /**
     * Clean tree to use it with the new API system.
     *
     * @param array $tree
     * @param Router $router
     * @param string|null $theme
     * @param null $search
     * @param string|null $module
     *
     * @return array
     */
    public function cleanTreeToApi($tree, Router $router, $theme = null, $search = null, $module = null)
    {
        $rootTree = [
            'tree' => [
                'total_translations' => 0,
                'total_missing_translations' => 0,
                'children' => [],
            ],
        ];

        $cleanTree = &$rootTree['tree']['children'];

        $index1 = 0;
        foreach ($tree as $k1 => $t1) {
            $index2 = 0;
            if (is_array($t1) && !str_starts_with($k1, '__')) {
                $this->addTreeInfo($router, $cleanTree, $index1, $k1, $k1, $this->theme, $search, $module);

                if (array_key_exists('__messages', $t1)) {
                    $nbMessage = count(current($t1['__messages']));
                    if (array_key_exists('__metadata', $t1)) {
                        --$nbMessage;
                    }

                    $cleanTree[$index1]['total_translations'] += $nbMessage;
                    $rootTree['tree']['total_translations'] += $nbMessage;

                    if (array_key_exists('__metadata', $t1) && array_key_exists('missing_translations', $t1['__metadata'])) {
                        $cleanTree[$index1]['total_missing_translations'] += (int) $t1['__metadata']['missing_translations'];
                        $rootTree['tree']['total_missing_translations'] += (int) $t1['__metadata']['missing_translations'];
                    }
                }

                foreach ($t1 as $k2 => $t2) {
                    $index3 = 0;
                    if (is_array($t2) && !str_starts_with($k2, '__')) {
                        $this->addTreeInfo($router, $cleanTree[$index1]['children'], $index2, $k2, $k1 . $k2, $this->theme, $search, $module);

                        if (array_key_exists('__messages', $t2)) {
                            $nbMessage = count(current($t2['__messages']));
                            if (array_key_exists('__metadata', $t2)) {
                                --$nbMessage;
                            }

                            $cleanTree[$index1]['children'][$index2]['total_translations'] += $nbMessage;
                            $cleanTree[$index1]['total_translations'] += $nbMessage;
                            $rootTree['tree']['total_translations'] += $nbMessage;

                            if (array_key_exists('__metadata', $t2) && array_key_exists('missing_translations', $t2['__metadata'])) {
                                $cleanTree[$index1]['children'][$index2]['total_missing_translations'] += (int) $t2['__metadata']['missing_translations'];
                                $cleanTree[$index1]['total_missing_translations'] += (int) $t2['__metadata']['missing_translations'];
                                $rootTree['tree']['total_missing_translations'] += (int) $t2['__metadata']['missing_translations'];
                            }
                        }

                        foreach ($t2 as $k3 => $t3) {
                            if (is_array($t3) && !str_starts_with($k3, '__')) {
                                $this->addTreeInfo($router, $cleanTree[$index1]['children'][$index2]['children'], $index3, $k3, $k1 . $k2 . $k3, $this->theme, $search, $module);

                                if (array_key_exists('__messages', $t3)) {
                                    $nbMessage = count(current($t3['__messages']));
                                    if (array_key_exists('__metadata', $t3)) {
                                        --$nbMessage;
                                    }

                                    $cleanTree[$index1]['children'][$index2]['children'][$index3]['total_translations'] += $nbMessage;
                                    $cleanTree[$index1]['children'][$index2]['total_translations'] += $nbMessage;
                                    $cleanTree[$index1]['total_translations'] += $nbMessage;
                                    $rootTree['tree']['total_translations'] += $nbMessage;
                                }

                                if (array_key_exists('__metadata', $t3) && array_key_exists('missing_translations', $t3['__metadata'])) {
                                    $cleanTree[$index1]['children'][$index2]['children'][$index3]['total_missing_translations'] += (int) $t3['__metadata']['missing_translations'];
                                    $cleanTree[$index1]['children'][$index2]['total_missing_translations'] += (int) $t3['__metadata']['missing_translations'];
                                    $cleanTree[$index1]['total_missing_translations'] += (int) $t3['__metadata']['missing_translations'];
                                    $rootTree['tree']['total_missing_translations'] += (int) $t3['__metadata']['missing_translations'];
                                }

                                if (empty($cleanTree[$index1]['children'][$index2]['children'][$index3]['children'])) {
                                    unset($cleanTree[$index1]['children'][$index2]['children'][$index3]['children']);
                                }
                                ++$index3;
                            }
                        }

                        if (empty($cleanTree[$index1]['children'][$index2]['children'])) {
                            unset($cleanTree[$index1]['children'][$index2]['children']);
                        }
                        ++$index2;
                    }
                }

                if (empty($cleanTree[$index1]['children'])) {
                    unset($cleanTree[$index1]['children']);
                }
                ++$index1;
            }
        }

        return $rootTree;
    }

    /**
     * @param Router $router
     * @param array $tree
     * @param int $index
     * @param string $name
     * @param string $fullName
     * @param string|bool $theme
     * @param string|null $search
     * @param string|bool $module
     *
     * @return mixed
     */
    private function addTreeInfo(Router $router, &$tree, $index, $name, $fullName, $theme = false, $search = null, $module = false)
    {
        if (!isset($tree[$index])) {
            $routeParams = [
                'locale' => $this->locale,
                'domain' => $fullName,
                'theme' => $theme,
                'module' => $module,
            ];

            if (!empty($search)) {
                $routeParams['search'] = $search;
            }

            $tree[$index]['name'] = $name;
            $tree[$index]['full_name'] = $fullName;
            $tree[$index]['domain_catalog_link'] = $router->generate('api_translation_domain_catalog', $routeParams);
            $tree[$index]['total_translations'] = 0;
            $tree[$index]['total_missing_translations'] = 0;
            $tree[$index]['children'] = [];
        }

        return $tree;
    }
}
