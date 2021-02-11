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

namespace PrestaShop\PrestaShop\Adapter\Number;

use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\CoreException;

class RoundModeConverter
{
    public const MODE_MAP = [
        PS_ROUND_UP => Rounding::ROUND_CEIL,
        PS_ROUND_DOWN => Rounding::ROUND_FLOOR,
        PS_ROUND_HALF_UP => Rounding::ROUND_HALF_UP,
        PS_ROUND_HALF_DOWN => Rounding::ROUND_HALF_DOWN,
        PS_ROUND_HALF_EVEN => Rounding::ROUND_HALF_EVEN,
    ];

    /**
     * @param int $legacyRoundMode
     *
     * @return string
     *
     * @throws CoreException
     */
    public static function getNumberRoundMode(int $legacyRoundMode): string
    {
        if (!isset(static::MODE_MAP[$legacyRoundMode])) {
            throw new CoreException(sprintf('Cannot map round mode %d', $legacyRoundMode));
        }

        return static::MODE_MAP[$legacyRoundMode];
    }
}
