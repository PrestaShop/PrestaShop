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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

/**
 * Interface FilterInterface defines contract for grid filter.
 */
interface FilterInterface
{
    /**
     * Get filter type to use.
     *
     * @return string Fully qualified filter type class name
     */
    public function getType();

    /**
     * Get filter name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set filter type options.
     *
     * @param array $filterTypeOptions
     *
     * @return self
     */
    public function setTypeOptions(array $filterTypeOptions);

    /**
     * Get filter type options.
     *
     * @return array
     */
    public function getTypeOptions();

    /**
     * Set column ID if filter is associated with column.
     *
     * @param string $columnId
     *
     * @return self
     */
    public function setAssociatedColumn($columnId);

    /**
     * Get associated column.
     *
     * @return string|null
     */
    public function getAssociatedColumn();
}
