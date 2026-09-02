<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @deprecated Since 9.0.0 for performance reasons and returns only empty array.
     *
     * @var CartInformation[]
     */
    private $cartsInformation;

    /**
     * @deprecated Since 9.0.0, returns empty ProductsInformation object with no data.
     *
     * @var ProductsInformation
     */
    private $productsInformation;

    /**
     * @var MessageInformation[]
     */
    private $messagesInformation;

    /**
     * @deprecated Since 9.0.0, returns only empty array.
     *
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
     * @deprecated Since 9.0.0, returns only empty array.
     *
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
     * @deprecated Since 9.0.0 for performance reasons and returns only empty array.
     *
     * @return CartInformation[]
     */
    public function getCartsInformation()
    {
        return $this->cartsInformation;
    }

    /**
     * @deprecated Since 9.0.0, returns empty ProductsInformation object with no data.
     *
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
     * @deprecated Since 9.0.0, returns only empty array.
     *
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
     * @deprecated Since 9.0.0, returns only empty array.
     *
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
