<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

enum DynamicDateRule: string
{
    case KEEP_AS_IS = 'keep_as_is';
    case TODAY = 'today';
    case YESTERDAY = 'yesterday';
    case CURRENT_WEEK = 'current_week';
    case CURRENT_MONTH = 'current_month';
    case CURRENT_QUARTER = 'current_quarter';
    case CURRENT_SEMESTER = 'current_semester';
    case CURRENT_YEAR = 'current_year';
    case LAST_DAYS = 'last_days';
}
