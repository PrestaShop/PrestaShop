<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\InvalidShipmentTrackingNumberException;

class TrackingNumber
{
    /**
     * @var string
     */
    private $trackingNumber;

    /**
     * @throws InvalidShipmentTrackingNumberException
     */
    public function __construct(string $trackingNumber)
    {
        $this->assertTrackingNumberNotEmptyOrWhitespace($trackingNumber);

        $this->trackingNumber = $trackingNumber;
    }

    public function getValue(): string
    {
        return $this->trackingNumber;
    }

    /**
     * @throws InvalidShipmentTrackingNumberException
     */
    private function assertTrackingNumberNotEmptyOrWhitespace(string $trackingNumber): void
    {
        if (trim($trackingNumber) === '') {
            throw new InvalidShipmentTrackingNumberException('Tracking number cannot be empty or whitespace.');
        }
    }
}
