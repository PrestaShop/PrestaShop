<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @var string|null
     */
    private $privateNote;

    /**
     * @var bool
     */
    private $isGuest;

    /**
     * @var string
     */
    private $ape;

    /**
     * @var string
     */
    private $siret;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @var array
     */
    private $groups;

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
     * @param bool $isGuest
     * @param int $languageId
     * @param string $ape
     * @param string $siret
     * @param array $groups
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
        ?string $privateNote,
        bool $isGuest,
        int $languageId,
        string $ape = '',
        string $siret = '',
        array $groups = []
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
        $this->isGuest = $isGuest;
        $this->languageId = $languageId;
        $this->ape = $ape;
        $this->siret = $siret;
        $this->groups = $groups;
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

    /**
     * @return bool
     */
    public function isGuest(): bool
    {
        return $this->isGuest;
    }

    /**
     * @return string
     */
    public function getApe(): string
    {
        return $this->ape;
    }

    /**
     * @return string
     */
    public function getSiret(): string
    {
        return $this->siret;
    }

    /**
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
