<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
    const ALLOWED_ADDRESS_TYPES = [
        OrderAddressType::INVOICE_ADDRESS_TYPE,
        OrderAddressType::DELIVERY_ADDRESS_TYPE,
    ];

    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var
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
     * @return mixed
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
