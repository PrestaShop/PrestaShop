<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\CartAddressType;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\InvalidAddressTypeException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Class EditCartAddressCommand used to edit a cart address and then update the related field
 * so that it uses the new duplicated address.
 */
class EditCartAddressCommand extends AbstractEditAddressCommand
{
    public const ALLOWED_ADDRESS_TYPES = [
        CartAddressType::INVOICE_ADDRESS_TYPE,
        CartAddressType::DELIVERY_ADDRESS_TYPE,
    ];

    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var string
     */
    private $addressType;

    /**
     * @param int $cartId
     * @param string $addressType
     *
     * @throws InvalidAddressTypeException
     * @throws CartConstraintException
     */
    public function __construct(
        int $cartId,
        string $addressType
    ) {
        $this->cartId = new CartId($cartId);
        $this->setAddressType($addressType);
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return string
     */
    public function getAddressType(): string
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
