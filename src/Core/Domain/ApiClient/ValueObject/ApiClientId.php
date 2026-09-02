<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;

class ApiClientId
{
    private int $apiClientId;

    public function __construct(int $apiClientId)
    {
        $this->assertIsIntegerGreaterThanZero($apiClientId);
        $this->apiClientId = $apiClientId;
    }

    public function getValue(): int
    {
        return $this->apiClientId;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param int $value
     *
     * @throws ApiClientConstraintException
     */
    private function assertIsIntegerGreaterThanZero($value)
    {
        if (!is_int($value) || 0 >= $value) {
            throw new ApiClientConstraintException(sprintf('Invalid api client id "%s".', var_export($value, true)), ApiClientConstraintException::INVALID_ID);
        }
    }
}
