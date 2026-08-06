<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

/**
 * Storage scope for extra property fields.
 *
 * String values match the scope ENUM column in extra_property_definition for DB compatibility.
 */
enum ExtraPropertyScope: string
{
    /** Stored in {entity}_extra — one row per entity row, not shop/lang dependent */
    case COMMON = 'common';

    /** Stored in {entity}_extra_lang — one row per entity × lang × shop */
    case LANG = 'lang';

    /** Stored in {entity}_extra_shop — one row per entity × shop */
    case SHOP = 'shop';
}
