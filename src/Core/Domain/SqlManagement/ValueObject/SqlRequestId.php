<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;

/**
 * Class SqlRequestId is SqlRequest identifier.
 */
class SqlRequestId
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $requestSqlId
     *
     * @throws SqlRequestException
     */
    public function __construct($requestSqlId)
    {
        if (!is_int($requestSqlId) || $requestSqlId <= 0) {
            throw new SqlRequestException(sprintf('Invalid SqlRequest id: %s', var_export($requestSqlId, true)));
        }

        $this->value = (int) $requestSqlId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}
