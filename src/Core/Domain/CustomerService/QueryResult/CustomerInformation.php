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
