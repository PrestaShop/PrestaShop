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
