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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
