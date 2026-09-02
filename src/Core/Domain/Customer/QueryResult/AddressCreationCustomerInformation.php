<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Provides customer data for address creation
 */
class AddressCreationCustomerInformation
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string|null
     */
    private $company;

    /**
     * @param int $customerId
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(int $customerId, string $firstName, string $lastName)
    {
        $this->customerId = new CustomerId($customerId);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string $company
     *
     * @return AddressCreationCustomerInformation
     */
    public function setCompany(string $company): AddressCreationCustomerInformation
    {
        $this->company = $company;

        return $this;
    }
}
