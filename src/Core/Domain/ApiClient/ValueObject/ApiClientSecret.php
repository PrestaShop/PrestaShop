<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;

class ApiClientSecret
{
    public const MIN_SIZE = 32;
    public const MAX_SIZE = 72;

    public function __construct(
        private string $value,
    ) {
        $this->assertSecretValue($this->value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertSecretValue(string $value)
    {
        if (strlen($value) < self::MIN_SIZE || strlen($value) > self::MAX_SIZE) {
            throw new ApiClientConstraintException(sprintf('Invalid api client secret "%s".', var_export($value, true)), ApiClientConstraintException::INVALID_SECRET);
        }
    }
}
