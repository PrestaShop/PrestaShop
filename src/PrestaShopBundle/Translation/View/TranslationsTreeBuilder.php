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

namespace PrestaShopBundle\Translation\View;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Builds a domain tree for the translation API
 */
class TranslationsTreeBuilder
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var string|null
     */
    private $theme;

    /**
     * @var array|null
     */
    private $search;

    /**
     * @var string|null
     */
    private $module;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Builds a domain tree ready to be sent via API
     *
     * @param array[] $translationArray
     * @param string $locale
     * @param string|null $theme
     * @param array|null $search
     * @param string|null $module
     *
     * @return array
     */
    public function buildDomainTreeForApi(
        array $translationArray,
        string $locale,
        ?array $search = null,
        ?string $theme = null,
        ?string $module = null
    ): array {
        $this->locale = $locale;
        $this->theme = $theme;
        $this->search = $search;
        $this->module = $module;

        $metadata = $this->buildDomainMetadataTree($translationArray);

        return [
            'tree' => $this->recursivelyBuildApiTree($metadata, null),
        ];
    }

    /**
     * Builds the API tree recursively by transforming the metadata subtree
     *
     * @param array $metadataSubtree A branch from the metadata tree
     * @param string|null $subtreeName Subtree name (eg. "Bar")
     * @param string|null $fullSubtreeName Full subtree name  (eg. "AdminFooBar")
     *
     * @return array API subtree
     */
    private function recursivelyBuildApiTree(
        array $metadataSubtree,
        ?string $subtreeName = null,
        ?string $fullSubtreeName = null
    ): array {
        $current = [];
        if ($subtreeName !== null) {
            $current['name'] = $subtreeName;
        }
        if ($fullSubtreeName !== null) {
            $current['full_name'] = $fullSubtreeName;
            $current['domain_catalog_link'] = $this->getRoute($fullSubtreeName);
        }

        foreach ($metadataSubtree as $name => $value) {
            if ($name === '__metadata') {
                $current['total_translations'] = $value['count'];
                $current['total_missing_translations'] = $value['missing_translations'];
                continue;
            }
            if (!isset($current['children'])) {
                $current['children'] = [];
            }

            $current['children'][] = $this->recursivelyBuildApiTree($value, $name, (string) $fullSubtreeName . $name);
        }

        return $current;
    }

    /**
     * Returns the URL path to the translations from the given domain in the current context
     *
     * @param string $fullName Domain name
     *
     * @return string URL path
     */
    private function getRoute(string $fullName): string
    {
        $routeParams = [
            'locale' => $this->locale,
            'domain' => $fullName,
            'theme' => $this->theme,
            'module' => $this->module,
        ];

        if (!empty($this->search)) {
            $routeParams['search'] = $this->search;
        }

        return $this->router->generate('api_translation_domain_catalog', $routeParams);
    }

    /**
     * @TODO This method will be added to TreeBuilder
     *
     * Builds a domain metadata tree.
     *
     * Returns a structure like this:
     *
     * ```
     * [
     *     '__metadata' => [
     *         'count' => 11,
     *         'missing_translations' => 5
     *     ],
     *     'Admin' => [
     *         '__metadata' => [
     *             'count' => 11,
     *             'missing_translations' => 5
     *         ],
     *         'Foo' => [
     *             '__metadata' => [
     *                 'count' => 4,
     *                 'missing_translations' => 3
     *             ],
     *             'Bar' => [
     *                 '__metadata' => [
     *                     'count' => 2,
     *                     'missing_translations' => 1
     *                 ],
     *             ],
     *             'Baz' => [
     *                 '__metadata' => [
     *                     'count' => 2,
     *                     'missing_translations' => 2
     *                 ],
     *             ],
     *         ],
     *         'Plop' => [
     *             '__metadata' => [
     *                 'count' => 7,
     *                 'missing_translations' => 2
     *             ],
     *             'Foo' => [
     *                 '__metadata' => [
     *                     'count' => 2,
     *                     'missing_translations' => 0
     *                 ],
     *             ],
     *             'Bar' => [
     *                 '__metadata' => [
     *                     'count' => 3,
     *                     'missing_translations' => 1
     *                 ],
     *             ],
     *         ],
     *     ],
     * ];
     * ```
     *
     * @param array $catalogue
     *
     * @return array
     */
    private function buildDomainMetadataTree(array $catalogue): array
    {
        // template for initializing metadata
        $emptyMeta = [
            'count' => 0,
            'missing_translations' => 0,
        ];

        $tree = [
            '__metadata' => $emptyMeta,
        ];

        // initialize tree structure
        foreach ($catalogue as $domain => $content) {
            $parts = $this->splitDomain($domain);

            // start at the root
            $subtree = &$tree;
            $currentSubdomainName = '';

            foreach ($parts as $partNumber => $part) {
                $subdomainPartName = ucfirst($part);
                $currentSubdomainName .= $subdomainPartName;

                // create domain part branch if it doesn't exist
                if (!array_key_exists($subdomainPartName, $subtree)) {
                    // only initialize tree leaves subtree with catalogue metadata
                    // branches are initialized with empty metadata (which will be updated later)
                    $isLastDomainPart = $partNumber === (count($parts) - 1);
                    $subtree[$subdomainPartName]['__metadata'] = ($isLastDomainPart && isset($content['__metadata']))
                        ? $content['__metadata']
                        : $emptyMeta;
                }

                // move pointer to said branch
                $subtree = &$subtree[$subdomainPartName];
            }
        }

        // update tree by aggregating branch metadata
        // eg. branch.meta = (child1.meta = (subchild1.meta + subchild2.meta + ... ) + child2.meta + ...)
        $this->updateCounters($tree);

        return $tree;
    }

    /**
     * @TODO This method will be added to TreeBuilder
     *
     * Updates counters of this subtree by adding the sum of children's counters
     *
     * @param array $subtree
     *
     * @return array Array of [sum of count, sum of missing_translations]
     */
    private function updateCounters(array &$subtree): array
    {
        foreach ($subtree as $key => $values) {
            if ($key === '__metadata') {
                continue;
            }

            // update child and get its counters
            list($count, $missing) = $this->updateCounters($subtree[$key]);

            // update this tree's counters by adding the child's
            $subtree['__metadata']['count'] += $count;
            $subtree['__metadata']['missing_translations'] += $missing;
        }

        return [
            $subtree['__metadata']['count'],
            $subtree['__metadata']['missing_translations'],
        ];
    }

    /**
     * @TODO This method will be added to TreeBuilder
     *
     * @param string $domain
     *
     * @return string[]
     */
    private function splitDomain(string $domain): array
    {
        // the third component of the domain may have underscores, so we need to limit pieces to 3
        return explode('_', Inflector::tableize($domain), 3);
    }
}
