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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Edit employee with given data.
 */
class EditEmployeeCommand
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
     * @var Password
     */
    private $plainPassword;

    /**
     * @var bool
     */
    private $hasEnabledGravatar = false;

    /**
     * @param int $employeeId
     */
    public function __construct($employeeId)
    {
        $this->employeeId = new EmployeeId((int) $employeeId);
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param EmployeeId $employeeId
     *
     * @return EditEmployeeCommand
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    /**
     * @return FirstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return EditEmployeeCommand
     */
    public function setFirstName($firstName)
    {
        $this->firstName = new FirstName($firstName);

        return $this;
    }

    /**
     * @return LastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return EditEmployeeCommand
     */
    public function setLastName($lastName)
    {
        $this->lastName = new LastName($lastName);

        return $this;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return EditEmployeeCommand
     */
    public function setEmail($email)
    {
        $this->email = new Email($email);

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultPageId()
    {
        return $this->defaultPageId;
    }

    /**
     * @param int $defaultPageId
     *
     * @return EditEmployeeCommand
     */
    public function setDefaultPageId($defaultPageId)
    {
        $this->defaultPageId = $defaultPageId;

        return $this;
    }

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @param int $languageId
     *
     * @return EditEmployeeCommand
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return EditEmployeeCommand
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @param int $profileId
     *
     * @return EditEmployeeCommand
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * @return array
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @param array $shopAssociation
     *
     * @return EditEmployeeCommand
     */
    public function setShopAssociation($shopAssociation)
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    /**
     * @return Password
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @param int $minLength
     * @param int $maxLength
     * @param int $minScore
     *
     * @return EditEmployeeCommand
     */
    public function setPlainPassword($plainPassword, int $minLength, int $maxLength, int $minScore)
    {
        $this->plainPassword = new Password($plainPassword, $minLength, $maxLength, $minScore);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasEnabledGravatar()
    {
        return $this->hasEnabledGravatar;
    }

    /**
     * @param bool $hasEnabledGravatar
     *
     * @return EditEmployeeCommand
     */
    public function setHasEnabledGravatar(bool $hasEnabledGravatar)
    {
        $this->hasEnabledGravatar = $hasEnabledGravatar;

        return $this;
    }
}
