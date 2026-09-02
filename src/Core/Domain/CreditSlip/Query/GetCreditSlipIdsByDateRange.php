<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CreditSlip\Query;

use DateTime;

/**
 * Gets CreditSlipIds for provided date range
 */
final class GetCreditSlipIdsByDateRange
{
    /**
     * @var DateTime
     */
    private $dateTimeFrom;

    /**
     * @var DateTime
     */
    private $dateTimeTo;

    /**
     * @param DateTime $dateTimeFrom
     * @param DateTime $dateTimeTo
     */
    public function __construct(DateTime $dateTimeFrom, DateTime $dateTimeTo)
    {
        $this->dateTimeFrom = $dateTimeFrom;
        $this->dateTimeTo = $dateTimeTo;
    }

    /**
     * @return DateTime
     */
    public function getDateTimeFrom()
    {
        return $this->dateTimeFrom;
    }

    /**
     * @return DateTime
     */
    public function getDateTimeTo()
    {
        return $this->dateTimeTo;
    }
}
