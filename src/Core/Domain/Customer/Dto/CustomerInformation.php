<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Dto;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Class CustomerInformation stores customer information for viewing in Back Office.
 */
class CustomerInformation
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
    private $customerOrdersInformation;

    /**
     * @var CartInformation[]
     */
    private $cartsInformation;

    /**
     * @var ProductsInformation
     */
    private $customerProductsInformation;

    /**
     * @var MessageInformation[]
     */
    private $customerMessagesInformation;

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
     * @var ReferrerInformation[]
     */
    private $referrersInformation;

    /**
     * @var AddressInformation[]
     */
    private $addressesInformation;

    /**
     * @param CustomerId $customerId
     * @param PersonalInformation $generalInformation
     * @param OrdersInformation $customerOrdersInformation
     * @param CartInformation[] $cartsInformation
     * @param ProductsInformation $customerProductsInformation
     * @param MessageInformation[] $customerMessagesInformation
     * @param DiscountInformation[] $discountsInformation
     * @param SentEmailInformation[] $sentEmailsInformation
     * @param LastConnectionInformation[] $lastConnectionsInformation
     * @param GroupInformation[] $groupsInformation
     * @param ReferrerInformation[] $referrersInformation
     * @param AddressInformation[] $addressesInformation
     */
    public function __construct(
        CustomerId $customerId,
        PersonalInformation $generalInformation,
        OrdersInformation $customerOrdersInformation,
        array $cartsInformation,
        ProductsInformation $customerProductsInformation,
        array $customerMessagesInformation,
        array $discountsInformation,
        array $sentEmailsInformation,
        array $lastConnectionsInformation,
        array $groupsInformation,
        array $referrersInformation,
        array $addressesInformation
    ) {
        $this->customerId = $customerId;
        $this->personalInformation = $generalInformation;
        $this->customerOrdersInformation = $customerOrdersInformation;
        $this->cartsInformation = $cartsInformation;
        $this->customerProductsInformation = $customerProductsInformation;
        $this->customerMessagesInformation = $customerMessagesInformation;
        $this->discountsInformation = $discountsInformation;
        $this->sentEmailsInformation = $sentEmailsInformation;
        $this->lastConnectionsInformation = $lastConnectionsInformation;
        $this->groupsInformation = $groupsInformation;
        $this->referrersInformation = $referrersInformation;
        $this->addressesInformation = $addressesInformation;
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
    public function getCustomerOrdersInformation()
    {
        return $this->customerOrdersInformation;
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
    public function getCustomerProductsInformation()
    {
        return $this->customerProductsInformation;
    }

    /**
     * @return MessageInformation[]
     */
    public function getCustomerMessagesInformation()
    {
        return $this->customerMessagesInformation;
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
     * @return ReferrerInformation[]
     */
    public function getReferrersInformation()
    {
        return $this->referrersInformation;
    }

    /**
     * @return AddressInformation[]
     */
    public function getAddressesInformation()
    {
        return $this->addressesInformation;
    }
}
