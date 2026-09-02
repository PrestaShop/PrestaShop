<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject;

use Exception;

class OrderDetailsId
{
    /**
     * @var array
     */
    private $orderDetailsId;

    /**
     * @param array $orderDetailsId
     *
     * @throws Exception
     */
    public function __construct(array $orderDetailsId)
    {
        if (0 >= count($orderDetailsId)) {
            throw new Exception('You need to provide at least one order detail ID');
        }

        $this->orderDetailsId = $orderDetailsId;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->orderDetailsId;
    }
}
