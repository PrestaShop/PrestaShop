<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Exception;

use Exception;

/**
 * Thrown on failure to delete all selected addresses without errors
 */
class BulkDeleteAddressException extends AddressException
{
    /**
     * @var int[]
     */
    private $addressIds;

    /**
     * @param int[] $addressIds
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(array $addressIds, $message = '', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->addressIds = $addressIds;
    }

    /**
     * @return int[]
     */
    public function getAddressIds(): array
    {
        return $this->addressIds;
    }
}
