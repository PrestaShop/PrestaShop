<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\ShopFilters;

/**
 * This builder instantiate a filters object of the specified type using
 * its default values for creation.
 */
final class ClassFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var string */
    private $filtersClass;

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        if (isset($config['filters_class'])) {
            $this->filtersClass = $config['filters_class'];
        }

        return parent::setConfig($config);
    }

    /**
     * Build the filters with the class defined by filtersClass
     *
     * @param Filters|null $filters
     *
     * @return Filters
     */
    public function buildFilters(?Filters $filters = null)
    {
        if (null === $this->filtersClass) {
            return $filters;
        }

        /** @var array $defaultParameters */
        $defaultParameters = call_user_func([$this->filtersClass, 'getDefaults']);
        if (null !== $filters) {
            $typedFilters = $this->constructFilters($filters->all(), $filters->getFilterId());
            $typedFilters->add($defaultParameters);
        } else {
            $typedFilters = $this->constructFilters($defaultParameters, $this->filterId);
        }

        return $typedFilters;
    }

    /**
     * This method is able to construct the Filters object, it relies on the fact that the constructors
     * always use the same parameters in the same order:
     *  - for Filters: array $filters, string $filterId
     *  - for ShopFilters: ShopConstraint $shopConstraint, array $filters, string $filterId
     *
     * @param array $filters
     * @param string $filterId
     *
     * @return Filters
     */
    private function constructFilters(array $filters, string $filterId): Filters
    {
        if (is_subclass_of($this->filtersClass, ShopFilters::class)) {
            return new $this->filtersClass($this->shopConstraint, $filters, $filterId);
        }

        return new $this->filtersClass($filters, $filterId);
    }
}
