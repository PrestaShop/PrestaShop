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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Class CustomerInformation stores customer information for viewing in Back Office.
 */
class ViewableCustomer
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var PersonalInformation
     */
    private $personalInformation;

    /**
     * @var OrdersInformation
     */
    private $ordersInformation;

    /**
     * @var CartInformation[]
     */
    private $cartsInformation;

    /**
     * @var ProductsInformation
     */
    private $productsInformation;

    /**
     * @var MessageInformation[]
     */
    private $messagesInformation;

    /**
     * @var DiscountInformation[]
     */
    private $discountsInformation;

    /**
     * @var SentEmailInformation[]
     */
    private $sentEmailsInformation;

    /**
     * @var LastConnectionInformation[]
     */
    private $lastConnectionsInformation;

    /**
     * @var GroupInformation[]
     */
    private $groupsInformation;

    /**
     * @var AddressInformation[]
     */
    private $addressesInformation;

    /**
     * @var GeneralInformation
     */
    private $generalInformation;

    /**
     * @param CustomerId $customerId
     * @param GeneralInformation $generalInformation
     * @param PersonalInformation $personalInformation
     * @param OrdersInformation $ordersInformation
     * @param CartInformation[] $cartsInformation
     * @param ProductsInformation $productsInformation
     * @param MessageInformation[] $messagesInformation
     * @param DiscountInformation[] $discountsInformation
     * @param SentEmailInformation[] $sentEmailsInformation
     * @param LastConnectionInformation[] $lastConnectionsInformation
     * @param GroupInformation[] $groupsInformation
     * @param AddressInformation[] $addressesInformation
     */
    public function __construct(
        CustomerId $customerId,
        GeneralInformation $generalInformation,
        PersonalInformation $personalInformation,
        OrdersInformation $ordersInformation,
        array $cartsInformation,
        ProductsInformation $productsInformation,
        array $messagesInformation,
        array $discountsInformation,
        array $sentEmailsInformation,
        array $lastConnectionsInformation,
        array $groupsInformation,
        array $addressesInformation
    ) {
        $this->customerId = $customerId;
        $this->personalInformation = $personalInformation;
        $this->ordersInformation = $ordersInformation;
        $this->cartsInformation = $cartsInformation;
        $this->productsInformation = $productsInformation;
        $this->messagesInformation = $messagesInformation;
        $this->discountsInformation = $discountsInformation;
        $this->sentEmailsInformation = $sentEmailsInformation;
        $this->lastConnectionsInformation = $lastConnectionsInformation;
        $this->groupsInformation = $groupsInformation;
        $this->addressesInformation = $addressesInformation;
        $this->generalInformation = $generalInformation;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return PersonalInformation
     */
    public function getPersonalInformation()
    {
        return $this->personalInformation;
    }

    /**
     * @return OrdersInformation
     */
    public function getOrdersInformation()
    {
        return $this->ordersInformation;
    }

    /**
     * @return CartInformation[]
     */
    public function getCartsInformation()
    {
        return $this->cartsInformation;
    }

    /**
     * @return ProductsInformation
     */
    public function getProductsInformation()
    {
        return $this->productsInformation;
    }

    /**
     * @return MessageInformation[]
     */
    public function getMessagesInformation()
    {
        return $this->messagesInformation;
    }

    /**
     * @return DiscountInformation[]
     */
    public function getDiscountsInformation()
    {
        return $this->discountsInformation;
    }

    /**
     * @return SentEmailInformation[]
     */
    public function getSentEmailsInformation()
    {
        return $this->sentEmailsInformation;
    }

    /**
     * @return LastConnectionInformation[]
     */
    public function getLastConnectionsInformation()
    {
        return $this->lastConnectionsInformation;
    }

    /**
     * @return GroupInformation[]
     */
    public function getGroupsInformation()
    {
        return $this->groupsInformation;
    }

    /**
     * @return AddressInformation[]
     */
    public function getAddressesInformation()
    {
        return $this->addressesInformation;
    }

    /**
     * @return GeneralInformation
     */
    public function getGeneralInformation()
    {
        return $this->generalInformation;
    }
}
