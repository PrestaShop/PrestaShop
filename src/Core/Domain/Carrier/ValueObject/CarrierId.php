<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;

/**
 * Contains carrier ID with it's constraints
 */
class CarrierId
{
    /**
     * @var int
     */
    private $carrierId;

    /**
     * @param int $carrierId
     */
    public function __construct(int $carrierId)
    {
        if (0 >= $carrierId) {
            throw new CarrierConstraintException(sprintf('Invalid carrier id "%d"', $carrierId), CarrierConstraintException::INVALID_ID);
        }
        $this->carrierId = $carrierId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->carrierId;
    }
}
