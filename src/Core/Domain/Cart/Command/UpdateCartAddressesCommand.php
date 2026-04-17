<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

class UpdateCartAddressesCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var AddressId
     */
    private $newDeliveryAddressId;

    /**
     * @var AddressId
     */
    private $newInvoiceAddressId;

    /**
     * @param int $cartId
     * @param int $newDeliveryAddressId
     * @param int $newInvoiceAddressId
     *
     * @throws AddressConstraintException
     * @throws CartConstraintException
     */
    public function __construct(int $cartId, int $newDeliveryAddressId, int $newInvoiceAddressId)
    {
        $this->cartId = new CartId($cartId);
        $this->setNewDeliveryAddressId($newDeliveryAddressId);
        $this->setNewInvoiceAddressId($newInvoiceAddressId);
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return AddressId
     */
    public function getNewDeliveryAddressId(): AddressId
    {
        return $this->newDeliveryAddressId;
    }

    /**
     * @return AddressId
     */
    public function getNewInvoiceAddressId(): AddressId
    {
        return $this->newInvoiceAddressId;
    }

    /**
     * @param int $newDeliveryAddressId
     *
     * @throws AddressConstraintException
     */
    private function setNewDeliveryAddressId(int $newDeliveryAddressId): void
    {
        $this->newDeliveryAddressId = new AddressId($newDeliveryAddressId);
    }

    /**
     * @param int $newInvoiceAddressId
     *
     * @throws AddressConstraintException
     */
    private function setNewInvoiceAddressId(int $newInvoiceAddressId): void
    {
        $this->newInvoiceAddressId = new AddressId($newInvoiceAddressId);
    }
}
