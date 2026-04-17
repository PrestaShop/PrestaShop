<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\DateTime;

use DateTimeImmutable;
use DateTimeZone;

/**
 * DateImmutable extends DateTimeImmutable to represent dates (without time) for API Platform.
 * It serializes/unserializes using Y-m-d format instead of full datetime format.
 */
class DateImmutable extends DateTimeImmutable
{
    public function __construct(string $datetime = 'now', ?DateTimeZone $timezone = null)
    {
        parent::__construct($datetime, $timezone);
    }
}
