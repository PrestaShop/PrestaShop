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

namespace PrestaShop\PrestaShop\Core\Translation\Builder;

use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Catalogue;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Builds a domain tree for the translation API.
 *
 * The tree will have any necessary information to display it in the interface :
 * domain names, counter, missing translations and link to access catalogue.
 */
class TranslationsTreeBuilder
{
    /**
     * @var Router
     */
    private $router;
    /**
     * @var TranslationCatalogueBuilder
     */
    private $translationCatalogueBuilder;

    /**
     * @param Router $router
     * @param TranslationCatalogueBuilder $translationCatalogueBuilder
     */
    public function __construct(Router $router, TranslationCatalogueBuilder $translationCatalogueBuilder)
    {
        $this->router = $router;
        $this->translationCatalogueBuilder = $translationCatalogueBuilder;
    }

    /**
     * @param ProviderDefinitionInterface $providerDefinition
     * @param string $locale
     * @param array $search
     *
     * @return array
     *
     * @throws TranslationFilesNotFoundException
     * @throws UnexpectedTranslationTypeException
     */
    public function getTree(
        ProviderDefinitionInterface $providerDefinition,
        string $locale,
        array $search
    ): array {
        $tree = $this->translationCatalogueBuilder->getRawCatalogue(
            $providerDefinition,
            $locale,
            $search
        )->buildTree();

        $routeParams = [
            'locale' => $locale,
            'theme' => $providerDefinition instanceof ThemeProviderDefinition ? $providerDefinition->getThemeName() : null,
            'module' => $providerDefinition instanceof ModuleProviderDefinition ? $providerDefinition->getModuleName() : null,
        ];
        if (!empty($search)) {
            $routeParams['search'] = $search;
        }

        return ['tree' => $this->recursivelyBuildApiTree($routeParams, $tree, null, null)];
    }

    /**
     * Builds the API tree recursively by transforming the metadata subtree
     *
     * @param array $routeParams
     * @param array $metadataSubtree A branch from the metadata tree
     * @param string|null $subtreeName Subtree name (eg. "Bar")
     * @param string|null $fullSubtreeName Full subtree name  (eg. "AdminFooBar")
     *
     * @return array API subtree
     */
    private function recursivelyBuildApiTree(
        array $routeParams,
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
            $current['domain_catalog_link'] = $this->getRoute($fullSubtreeName, $routeParams);
        }

        foreach ($metadataSubtree as $name => $value) {
            if ($name === Catalogue::METADATA_KEY_NAME) {
                $current['total_translations'] = $value['count'];
                $current['total_missing_translations'] = $value['missing_translations'];
                continue;
            }
            if (!isset($current['children'])) {
                $current['children'] = [];
            }

            $current['children'][] = $this->recursivelyBuildApiTree($routeParams, $value, $name, (string) $fullSubtreeName . $name);
        }

        if (isset($current['children'])) {
            usort($current['children'], function (array $child1, array $child2) {
                return strcmp($child1['name'], $child2['name']);
            });
        }

        return $current;
    }

    /**
     * Returns the URL path to the translations from the given domain in the current context
     *
     * @param string $fullName Domain name
     * @param array $routeParams
     *
     * @return string URL path
     */
    private function getRoute(string $fullName, array $routeParams): string
    {
        $routeParams['domain'] = $fullName;

        return $this->router->generate('api_translation_domain_catalog', $routeParams);
    }
}
