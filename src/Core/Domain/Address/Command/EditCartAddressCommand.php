<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
