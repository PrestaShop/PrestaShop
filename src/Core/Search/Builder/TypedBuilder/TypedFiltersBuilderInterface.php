<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder;

use PrestaShop\PrestaShop\Core\Search\Builder\ClassFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Builder\FiltersBuilderInterface;

/**
 * The filters build process is based on multi layers among which we have one that adapts
 * generic filters to specific classes, which allows overriding default values and filterId
 * easily.
 *
 * But some Filters classes may need some specific way of being built so you can define a specific
 * builder service that is handled by @see ClassFiltersBuilder which contains various specified
 * builders, if the built type is identified by TypedFiltersBuilderInterface::supports method then
 * this builder will be used instead of the generic construction.
 */
interface TypedFiltersBuilderInterface extends FiltersBuilderInterface
{
    /**
     * @param string $filterClassName
     *
     * @return bool
     */
    public function supports(string $filterClassName): bool;
}
