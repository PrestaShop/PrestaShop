<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderCustomerForViewing
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string Gender name
     */
    private $lastName;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $email;

    /**
     * @var DateTimeImmutable
     */
    private $accountRegistrationDate;

    /**
     * @var string Formatted price with currency
     */
    private $totalSpentSinceRegistration;

    /**
     * @var int
     */
    private $validOrdersPlaced;

    /**
     * @var string
     */
    private $privateNote;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $gender
     * @param string $email
     * @param DateTimeImmutable $accountRegistrationDate
     * @param string $totalSpentSinceRegistration
     * @param int $validOrdersPlaced
     * @param string|null $privateNote
     */
    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $gender,
        string $email,
        DateTimeImmutable $accountRegistrationDate,
        string $totalSpentSinceRegistration,
        int $validOrdersPlaced,
        ?string $privateNote
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->gender = $gender;
        $this->email = $email;
        $this->accountRegistrationDate = $accountRegistrationDate;
        $this->totalSpentSinceRegistration = $totalSpentSinceRegistration;
        $this->validOrdersPlaced = $validOrdersPlaced;
        $this->privateNote = $privateNote;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getAccountRegistrationDate(): DateTimeImmutable
    {
        return $this->accountRegistrationDate;
    }

    /**
     * @return string
     */
    public function getTotalSpentSinceRegistration(): string
    {
        return $this->totalSpentSinceRegistration;
    }

    /**
     * @return int
     */
    public function getValidOrdersPlaced(): int
    {
        return $this->validOrdersPlaced;
    }

    /**
     * @return string|null
     */
    public function getPrivateNote(): ?string
    {
        return $this->privateNote;
    }
}
