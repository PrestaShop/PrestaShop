<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;

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
     * @var string|null
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
     * @param CustomerId $customerId
     */
    public function __construct(CustomerId $customerId)
    {
        $this->customerId = $customerId;
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
     * @param string|FirstName $firstName
     *
     * @return self
     */
    public function setFirstName($firstName)
    {
        if (!$firstName instanceof FirstName) {
            $firstName = new FirstName($firstName);
        }

        $this->firstName = $firstName;

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
     * @param string|LastName $lastName
     *
     * @return self
     */
    public function setLastName($lastName)
    {
        if (!$lastName instanceof LastName) {
            $lastName = new LastName($lastName);
        }

        $this->lastName = $lastName;

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
     * @param string|Email $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        if (!$email instanceof Email) {
            $email = new Email($email);
        }

        $this->email = $email;

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
     * @param string|Password $password
     *
     * @return self
     */
    public function setPassword($password)
    {
        if (!$password instanceof Password) {
            $password = new Password($password);
        }

        $this->password = $password;

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
     * @param int|null $defaultGroupId
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
     * @param int[]|null $groupIds
     *
     * @return self
     */
    public function setGroupIds($groupIds)
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
     * @param int|null $genderId
     *
     * @return self
     */
    public function setGenderId($genderId)
    {
        $this->genderId = $genderId;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool|null $isEnabled
     *
     * @return self
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isPartnerOffersSubscribed()
    {
        return $this->isPartnerOffersSubscribed;
    }

    /**
     * @param bool|null $isPartnerOffersSubscribed
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
     * @param Birthday $birthday
     *
     * @return self
     */
    public function setBirthday(Birthday $birthday)
    {
        $this->birthday = $birthday;

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
     * @param string|null $companyName
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
     * @param string|null $siretCode
     *
     * @return self
     */
    public function setSiretCode($siretCode)
    {
        $this->siretCode = $siretCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getApeCode()
    {
        return $this->apeCode;
    }

    /**
     * @param string|null $apeCode
     *
     * @return self
     */
    public function setApeCode($apeCode)
    {
        $this->apeCode = $apeCode;

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
     * @param string|null $website
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
     * @param float|null $allowedOutstandingAmount
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
     * @param int|null $maxPaymentDays
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
     * @param int|null $riskId
     *
     * @return self
     */
    public function setRiskId($riskId)
    {
        $this->riskId = $riskId;

        return $this;
    }
}
