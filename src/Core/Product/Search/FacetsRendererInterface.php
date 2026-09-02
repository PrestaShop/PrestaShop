<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

/**
 * Define how we render facets and active filters.
 */
interface FacetsRendererInterface
{
    /**
     * @param ProductSearchContext $context
     * @param ProductSearchResult $result
     *
     * @return string HTML content is expected here
     */
    public function renderFacets(
        ProductSearchContext $context,
        ProductSearchResult $result
    );

    /**
     * @param ProductSearchContext $context
     * @param ProductSearchResult $result
     *
     * @return string HTML content is expected here
     */
    public function renderActiveFilters(
        ProductSearchContext $context,
        ProductSearchResult $result
    );
}
