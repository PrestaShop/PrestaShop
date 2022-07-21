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

namespace PrestaShop\PrestaShop\Core\Search;

/**
 * Class Pagination
 * defines pagination methods according
 * PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface
 * search parameters definitions
 * (offset starts at 0, null Limit imply that no limit used, etc.)
 */
class Pagination
{
    public static function isOffsetOutOfRange(int $total, int $offset = null): bool
    {
        return $total > 0 && $offset >= $total;
    }

    /**
     * @param int $total
     * @param int $offset
     *
     * @return int offset if given offset is valid, it will be returned (0 if null given)
     */
    public static function computeValidOffset(int $total, int $offset = null, int $limit = null): int
    {
        if (static::isOffsetOutOfRange($total, $offset)) {
            if (empty($limit) || $limit > $total) {
                return 0;
            } else {
                return $total - $limit;
            }
        }

        return (int) $offset;
    }
}
