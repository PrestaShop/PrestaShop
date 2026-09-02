<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Adds new employee with given data
 */
class AddEmployeeCommand
{
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
     * @var Password
     */
    private $plainPassword;

    /**
     * @var bool
     */
    private $hasEnabledGravatar;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $plainPassword
     * @param int $defaultPageId
     * @param int $languageId
     * @param bool $active
     * @param int $profileId
     * @param array $shopAssociation
     * @param bool $hasEnabledGravatar
     * @param int $minLength
     * @param int $maxLength
     * @param int $minScore
     */
    public function __construct(
        $firstName,
        $lastName,
        $email,
        $plainPassword,
        $defaultPageId,
        $languageId,
        $active,
        $profileId,
        array $shopAssociation,
        bool $hasEnabledGravatar,
        int $minLength,
        int $maxLength,
        int $minScore
    ) {
        $this->firstName = new FirstName($firstName);
        $this->lastName = new LastName($lastName);
        $this->email = new Email($email);
        $this->defaultPageId = $defaultPageId;
        $this->languageId = $languageId;
        $this->active = $active;
        $this->profileId = $profileId;
        $this->shopAssociation = $shopAssociation;
        $this->plainPassword = new Password($plainPassword, $minLength, $maxLength, $minScore);
        $this->hasEnabledGravatar = $hasEnabledGravatar;
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
     * @return Password
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return bool
     */
    public function hasEnabledGravatar()
    {
        return $this->hasEnabledGravatar;
    }
}
