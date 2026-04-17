<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Stores editable data of an employee.
 */
class EditableEmployee
{
    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * @var FirstName
     */
    private $firstName;

    /**
     * @var LastName
     */
    private $lastName;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var int
     */
    private $defaultPageId;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var int
     */
    private $profileId;

    /**
     * @var array
     */
    private $shopAssociation;

    /**
     * @var string
     */
    private $avatarUrl;

    /**
     * @var bool
     */
    private $hasEnabledGravatar;

    /**
     * @param EmployeeId $employeeId
     * @param FirstName $firstName
     * @param LastName $lastName
     * @param Email $email
     * @param string $avatarUrl
     * @param int $defaultPageId
     * @param int $languageId
     * @param bool $active
     * @param int $profileId
     * @param array $shopAssociation
     * @param bool $hasEnabledGravatar
     */
    public function __construct(
        EmployeeId $employeeId,
        FirstName $firstName,
        LastName $lastName,
        Email $email,
        $avatarUrl,
        $defaultPageId,
        $languageId,
        $active,
        $profileId,
        array $shopAssociation,
        bool $hasEnabledGravatar = false
    ) {
        $this->employeeId = $employeeId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->defaultPageId = $defaultPageId;
        $this->languageId = $languageId;
        $this->active = $active;
        $this->profileId = $profileId;
        $this->shopAssociation = $shopAssociation;
        $this->avatarUrl = $avatarUrl;
        $this->hasEnabledGravatar = $hasEnabledGravatar;
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @return FirstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return LastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getDefaultPageId()
    {
        return $this->defaultPageId;
    }

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return array
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    /**
     * @return bool
     */
    public function hasEnabledGravatar()
    {
        return $this->hasEnabledGravatar;
    }
}
