<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Interface for filters builders, each builder needs a config which is provided
 * through the setConfig method which MUST be called before calling buildFilters.
 *
 * As the builders are called one after the other they may be provided with an
 * existing filters instance that they need to override, if not they simply create
 * a new Filters instance.
 */
interface FiltersBuilderInterface
{
    public const FILTER_TYPES = [
        'limit',
        'offset',
        'orderBy',
        'sortOrder',
        'filters',
    ];

    /**
     * Allows to set a config through an associative array, this method should
     * be called before buildFilters, it returns the builder for convenience so
     * you can chain both calls (e.g: $builder->setConfig($config)->buildFilters())
     *
     * @param array $config
     *
     * @return $this
     */
    public function setConfig(array $config);

    /**
     * This method is called to build the filters, the filters parameter is used if
     * you want to override a pre existing filter. All builders should keep that in
     * mind as they can be used consecutively with other builders and must not drop
     * existing values carelessly.
     *
     * @param Filters|null $filters
     *
     * @return Filters
     */
    public function buildFilters(?Filters $filters = null);
}
