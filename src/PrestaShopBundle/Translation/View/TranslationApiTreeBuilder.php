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

namespace PrestaShopBundle\Translation\View;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Builds a domain tree for the translation API
 */
class TranslationApiTreeBuilder
{

    /**
     * @var Router
     */
    private $router;

    /**
     * @var TreeBuilder
     */
    private $treeBuilder;

    /**
     * @var string|null
     */
    private $theme;

    /**
     * @var string|null
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
     * @param TreeBuilder $treeBuilder
     */
    public function __construct(Router $router, TreeBuilder $treeBuilder)
    {
        $this->router = $router;
        $this->treeBuilder = $treeBuilder;
    }

    /**
     * Builds a domain tree ready to be sent via API
     *
     * @param array[] $translationArray
     * @param string $locale
     * @param string|null $theme
     * @param string|null $search
     * @param string|null $module
     *
     * @return array
     */
    public function buildDomainTreeForApi(array $translationArray, $locale, $theme = null, $search = null, $module = null)
    {
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
    private function recursivelyBuildApiTree($metadataSubtree, $subtreeName = null, $fullSubtreeName = null) {
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
    private function getRoute($fullName)
    {
        $routeParams = array(
            'locale' => $this->locale,
            'domain' => $fullName,
            'theme' => $this->theme,
            'module' => $this->module,
        );

        if (!empty($this->search)) {
            $routeParams['search'] = $this->search;
        }

        return $this->router->generate('api_translation_domain_catalog', $routeParams);
    }

    /**
     * Builds a metadata tree with aggregate information per subdomain
     *
     * @param array[] $translationsArray
     *
     * @return array[] Metadata tree
     */
    private function buildDomainMetadataTree(array $translationsArray) {
        return $this->treeBuilder->buildDomainMetadataTree($translationsArray);
    }

}
