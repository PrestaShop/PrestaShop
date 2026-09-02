<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;

/**
 * Carriers are referenced by id_reference (instead of usual primary id as most entities)
 */
class CarrierReferenceId
{
    /**
     * @var int
     */
    private $carrierReferenceId;

    /**
     * @param int $carrierReferenceId
     *
     * @throws CarrierConstraintException
     */
    public function __construct($carrierReferenceId)
    {
        $this->assertIntegerIsGreaterThanZero($carrierReferenceId);
        $this->carrierReferenceId = $carrierReferenceId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->carrierReferenceId;
    }

    /**
     * @param int $carrierReferenceId
     */
    private function assertIntegerIsGreaterThanZero(int $carrierReferenceId)
    {
        if (0 >= $carrierReferenceId) {
            throw new CarrierConstraintException(
                sprintf('CarrierReferenceId "%s" is invalid. It must greater than 0.', $carrierReferenceId),
                CarrierConstraintException::INVALID_REFERENCE_ID
            );
        }
    }
}
