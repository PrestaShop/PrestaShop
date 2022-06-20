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

namespace PrestaShop\PrestaShop\Core\Util;

class Sorter
{
    public const ORDER_ASC = 'ASC';
    public const ORDER_DESC = 'DESC';

    /**
     * @param array<array<string, mixed>> $array
     * @param string $order
     * @param string ...$criterias
     *
     * @return array
     */
    public function natural(array $array, string $order, string ...$criterias): array
    {
        usort($array, function ($a, $b) use ($order, $criterias) {
            $cmp = 0;
            foreach ($criterias as $criteria) {
                if (!isset($a[$criteria]) || !isset($b[$criteria])) {
                    return 0;
                }
                $cmp = strnatcmp($a[$criteria], $b[$criteria]);
                if ($cmp !== 0) {
                    break;
                }
            }

            return static::ORDER_DESC === $order ? $cmp : $cmp * -1;
        });

        return $array;
    }
}
