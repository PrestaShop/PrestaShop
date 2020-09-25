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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\ApeCode;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Edits provided customer.
 * It can edit either all or partial data.
 *
 * Only not-null values are considered when editing customer.
 * For example, if the email is null, then the original value is not modified,
 * however, if email is set, then the original value will be overwritten.
 */
class EditCustomerCommand
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var FirstName|null
     */
    private $firstName;

    /**
     * @var LastName|null
     */
    private $lastName;

    /**
     * @var Email|null
     */
    private $email;

    /**
     * @var Password|null
     */
    private $password;

    /**
     * @var int|null
     */
    private $defaultGroupId;

    /**
     * @var int[]|null
     */
    private $groupIds;

    /**
     * @var int|null
     */
    private $genderId;

    /**
     * @var bool|null
     */
    private $isNewsletterSubscribed;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var bool|null
     */
    private $isPartnerOffersSubscribed;

    /**
     * @var Birthday|null
     */
    private $birthday;

    /**
     * @var string|null
     */
    private $companyName;

    /**
     * @var string|null
     */
    private $siretCode;

    /**
     * @var ApeCode|null
     */
    private $apeCode;

    /**
     * @var string|null
     */
    private $website;

    /**
     * @var float|null
     */
    private $allowedOutstandingAmount;

    /**
     * @var int|null
     */
    private $maxPaymentDays;

    /**
     * @var int|null
     */
    private $riskId;

    /**
     * @param int $customerId
     */
    public function __construct($customerId)
    {
        $this->customerId = new CustomerId($customerId);
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return FirstName|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = new FirstName($firstName);

        return $this;
    }

    /**
     * @return LastName|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = new LastName($lastName);

        return $this;
    }

    /**
     * @return Email|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = new Email($email);

        return $this;
    }

    /**
     * @return Password|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = new Password($password);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /**
     * @param int $defaultGroupId
     *
     * @return self
     */
    public function setDefaultGroupId($defaultGroupId)
    {
        $this->defaultGroupId = $defaultGroupId;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getGroupIds()
    {
        return $this->groupIds;
    }

    /**
     * @param int[] $groupIds
     *
     * @return self
     */
    public function setGroupIds(array $groupIds)
    {
        $this->groupIds = $groupIds;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGenderId()
    {
        return $this->genderId;
    }

    /**
     * @param int $genderId
     *
     * @return self
     */
    public function setGenderId($genderId)
    {
        $this->genderId = $genderId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNewsletterSubscribed()
    {
        return $this->isNewsletterSubscribed;
    }

    /**
     * @param bool $isNewsletterSubscribed
     */
    public function setNewsletterSubscribed($isNewsletterSubscribed)
    {
        $this->isNewsletterSubscribed = $isNewsletterSubscribed;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     *
     * @return self
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPartnerOffersSubscribed()
    {
        return $this->isPartnerOffersSubscribed;
    }

    /**
     * @param bool $isPartnerOffersSubscribed
     *
     * @return self
     */
    public function setIsPartnerOffersSubscribed($isPartnerOffersSubscribed)
    {
        $this->isPartnerOffersSubscribed = $isPartnerOffersSubscribed;

        return $this;
    }

    /**
     * @return Birthday|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     *
     * @return self
     */
    public function setBirthday($birthday)
    {
        $this->birthday = new Birthday($birthday);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     *
     * @return self
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiretCode()
    {
        return $this->siretCode;
    }

    /**
     * @param string $siretCode
     *
     * @return self
     */
    public function setSiretCode($siretCode)
    {
        $this->siretCode = $siretCode;

        return $this;
    }

    /**
     * @return ApeCode|null
     */
    public function getApeCode()
    {
        return $this->apeCode;
    }

    /**
     * @param string $apeCode
     *
     * @return self
     */
    public function setApeCode($apeCode)
    {
        $this->apeCode = new ApeCode($apeCode);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     *
     * @return self
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAllowedOutstandingAmount()
    {
        return $this->allowedOutstandingAmount;
    }

    /**
     * @param float $allowedOutstandingAmount
     *
     * @return self
     */
    public function setAllowedOutstandingAmount($allowedOutstandingAmount)
    {
        $this->allowedOutstandingAmount = $allowedOutstandingAmount;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxPaymentDays()
    {
        return $this->maxPaymentDays;
    }

    /**
     * @param int $maxPaymentDays
     *
     * @return self
     */
    public function setMaxPaymentDays($maxPaymentDays)
    {
        $this->maxPaymentDays = $maxPaymentDays;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRiskId()
    {
        return $this->riskId;
    }

    /**
     * @param int $riskId
     *
     * @return self
     */
    public function setRiskId($riskId)
    {
        $this->riskId = $riskId;

        return $this;
    }
}
