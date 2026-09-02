<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

/**
 * Carries data about customer for customer thread
 */
class CustomerInformation
{
    /**
     * @var int|null
     */
    private $customerId;

    /**
     * @var string|null
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var int|null
     */
    private $validatedOrdersCount;

    /**
     * @var string|null
     */
    private $validatedOrdersAmount;

    /**
     * @var string|null
     */
    private $customerSinceDate;

    /**
     * @param string $email
     *
     * @return self
     */
    public static function withEmailOnly($email)
    {
        return new self(null, null, null, $email, null, null, null);
    }

    /**
     * @param int|null $customerId
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string $email
     * @param int|null $validatedOrdersCount
     * @param string|null $validatedOrdersAmount
     * @param string|null $customerSinceDate
     */
    public function __construct(
        $customerId,
        $firstName,
        $lastName,
        $email,
        $validatedOrdersCount,
        $validatedOrdersAmount,
        $customerSinceDate
    ) {
        $this->customerId = $customerId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->validatedOrdersCount = $validatedOrdersCount;
        $this->validatedOrdersAmount = $validatedOrdersAmount;
        $this->customerSinceDate = $customerSinceDate;
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int|null
     */
    public function getValidatedOrdersCount()
    {
        return $this->validatedOrdersCount;
    }

    /**
     * @return string|null
     */
    public function getValidatedOrdersAmount()
    {
        return $this->validatedOrdersAmount;
    }

    /**
     * @return string|null
     */
    public function getCustomerSinceDate()
    {
        return $this->customerSinceDate;
    }
}
