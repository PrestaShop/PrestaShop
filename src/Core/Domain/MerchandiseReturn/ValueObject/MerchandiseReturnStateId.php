<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnOrderStateConstraintException;

/**
 * @todo I don't know whether this should be MerchandiseReturnStateId or OrderReturnStateId
 */
class MerchandiseReturnStateId
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     *
     * @throws MerchandiseReturnOrderStateConstraintException
     */
    public function __construct(int $id)
    {
        $this->assertIsIntegerGreaterThanZero($id);
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->id;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param $value
     *
     * @throws MerchandiseReturnOrderStateConstraintException
     */
    private function assertIsIntegerGreaterThanZero($value)
    {
        if (!is_int($value) || 0 >= $value) {
            throw new MerchandiseReturnOrderStateConstraintException(sprintf('Invalid merchandise return order state id "%s".', var_export($value, true)));
        }
    }
}
