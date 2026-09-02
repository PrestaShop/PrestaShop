<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAddressTypeException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderAddressType;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Class EditOrderAddressCommand used to edit an order address and then update the related field
 * so that it uses the new duplicated address.
 */
class EditOrderAddressCommand extends AbstractEditAddressCommand
{
    public const ALLOWED_ADDRESS_TYPES = [
        OrderAddressType::INVOICE_ADDRESS_TYPE,
        OrderAddressType::DELIVERY_ADDRESS_TYPE,
    ];

    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var string
     */
    private $addressType;

    /**
     * @param int $orderId
     * @param string $addressType
     *
     * @throws InvalidAddressTypeException
     * @throws OrderException
     */
    public function __construct(
        int $orderId,
        string $addressType
    ) {
        $this->orderId = new OrderId($orderId);
        $this->setAddressType($addressType);
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getAddressType()
    {
        return $this->addressType;
    }

    /**
     * @param string $addressType
     *
     * @throws InvalidAddressTypeException
     */
    private function setAddressType(string $addressType): void
    {
        if (!in_array($addressType, self::ALLOWED_ADDRESS_TYPES)) {
            throw new InvalidAddressTypeException(sprintf(
                'Invalid address type %s, allowed values are: %s',
                $addressType,
                implode(',', self::ALLOWED_ADDRESS_TYPES)
            ));
        }

        $this->addressType = $addressType;
    }
}
