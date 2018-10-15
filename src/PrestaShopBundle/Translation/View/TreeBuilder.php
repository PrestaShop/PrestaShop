<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\View;

use PrestaShopBundle\Translation\Provider\AbstractProvider;
use Doctrine\Common\Util\Inflector;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class TreeBuilder
{
    private $locale;
    private $theme;

    public function __construct($locale, $theme)
    {
        $this->locale = $locale;
        $this->theme = $theme;
    }

    /**
     * @param AbstractProvider $provider
     * @param null $search
     *
     * @return array|mixed
     */
    public function makeTranslationArray(AbstractProvider $provider, $search = null)
    {
        $provider->setLocale($this->locale);

        if ('theme' === $provider->getIdentifier()) {
            $translations = $provider->getMessageCatalogue()->all();
        } else {
            $translations = $provider->getDefaultCatalogue()->all();
        }

        $xliffCatalog = $provider->getXliffCatalogue()->all();
        $databaseCatalogue = $provider->getDatabaseCatalogue($this->theme)->all();

        foreach ($translations as $domain => $messages) {
            $missingTranslations = 0;
            $domainDatabase = str_replace('.' . $provider->getLocale(), '', $domain);

            foreach ($messages as $translationKey => $translationValue) {
                $data = array(
                    'xlf' => (array_key_exists($domain, $xliffCatalog) &&
                    array_key_exists($translationKey, $xliffCatalog[$domain]) ?
                        $xliffCatalog[$domain][$translationKey] : null),
                    'db' => (array_key_exists($domainDatabase, $databaseCatalogue) &&
                    array_key_exists($translationKey, $databaseCatalogue[$domainDatabase]) ?
                        $databaseCatalogue[$domainDatabase][$translationKey] : null),
                );

                // if search is empty or is in catalog default|xlf|database
                if (empty($search) || $this->dataContainsSearchWord($search, array_merge(array('default' => $translationKey), $data))) {
                    $translations[$domain][$translationKey] = $data;

                    if (
                        empty($data['xlf']) &&
                        empty($data['db'])
                    ) {
                        ++$missingTranslations;
                    }
                } else {
                    unset($translations[$domain][$translationKey]);
                }
            }

            $translations[$domain]['__metadata'] = array('missing_translations' => $missingTranslations);
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

    /**
     * @return array
     */
    public function makeTranslationsTree($catalogue)
    {
        $translationsTree = array();

        foreach ($catalogue as $domain => $messages) {
            $tableisedDomain = Inflector::tableize($domain);
            list($basename) = explode('.', $tableisedDomain);
            $parts = array_reverse(explode('_', $basename));
            $subtree = &$translationsTree;

            while (count($parts) > 0) {
                $subdomain = ucfirst(array_pop($parts));

                if (!array_key_exists($subdomain, $subtree)) {
                    $subtree[$subdomain] = array();
                }

                $subtree = &$subtree[$subdomain];
            }

            $subtree['__messages'] = array($domain => $messages);
            if (isset($messages['__metadata'])) {
                $subtree['__fixed_length_id'] = '_' . sha1($domain);
                list($subtree['__domain']) = explode('.', $domain);
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
     * @param $tree
     * @param Router $router
     * @param null $theme
     * @param null $search
     *
     * @return array
     */
    public function cleanTreeToApi($tree, Router $router, $theme = null, $search = null)
    {
        $rootTree = array(
            'tree' => array(
                'total_translations' => 0,
                'total_missing_translations' => 0,
                'children' => array(),
            ),
        );

        $cleanTree = &$rootTree['tree']['children'];

        $index1 = 0;
        foreach ($tree as $k1 => $t1) {
            $index2 = 0;
            if (is_array($t1) && '__' !== substr($k1, 0, 2)) {
                $this->addTreeInfo($router, $cleanTree, $index1, $k1, $k1, $theme, $search);

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
                    if (is_array($t2) && '__' !== substr($k2, 0, 2)) {
                        $this->addTreeInfo($router, $cleanTree[$index1]['children'], $index2, $k2, $k1 . $k2, $theme, $search);

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
                            if (is_array($t3) && '__' !== substr($k3, 0, 2)) {
                                $this->addTreeInfo($router, $cleanTree[$index1]['children'][$index2]['children'], $index3, $k3, $k1 . $k2 . $k3, $theme, $search);

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
     * @param $tree
     * @param $index
     * @param $name
     * @param $fullName
     * @param bool $theme
     * @param null $search
     *
     * @return mixed
     */
    private function addTreeInfo(Router $router, &$tree, $index, $name, $fullName, $theme = false, $search = null)
    {
        if (!isset($tree[$index])) {
            $routeParams = array(
                'locale' => $this->locale,
                'domain' => $fullName,
                'theme' => $theme,
            );

            if (!empty($search)) {
                $routeParams['search'] = $search;
            }

            $tree[$index]['name'] = $name;
            $tree[$index]['full_name'] = $fullName;
            $tree[$index]['domain_catalog_link'] = $router->generate('api_translation_domain_catalog', $routeParams);
            $tree[$index]['total_translations'] = 0;
            $tree[$index]['total_missing_translations'] = 0;
            $tree[$index]['children'] = array();
        }

        return $tree;
    }
}
