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
    public function buildFilters(Filters $filters = null)
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
