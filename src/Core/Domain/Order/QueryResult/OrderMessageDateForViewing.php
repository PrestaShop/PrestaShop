<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

/**
 * Used in order page view to display date in wanted format.
 */
class OrderMessageDateForViewing
{
    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $dateFormat;

    public function __construct(DateTimeImmutable $date, string $dateFormat)
    {
        $this->date = $date;
        $this->dateFormat = $dateFormat;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->date->format($this->dateFormat);
    }
}
