<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;

/**
 * Stores editable data for customer
 */
class EditableCustomer
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var int
     */
    private $genderId;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var Birthday
     */
    private $birthday;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var bool
     */
    private $isPartnerOffersSubscribed;

    /**
     * @var bool
     */
    private $isNewsletterSubscribed;

    /**
     * @var array|int[]
     */
    private $groupIds;

    /**
     * @var int
     */
    private $defaultGroupId;

    /**
     * @var string
     */
    private $companyName;

    /**
     * @var string
     */
    private $siretCode;

    /**
     * @var string
     */
    private $apeCode;

    /**
     * @var string
     */
    private $website;

    /**
     * @var float
     */
    private $allowedOutstandingAmount;

    /**
     * @var int
     */
    private $maxPaymentDays;

    /**
     * @var int
     */
    private $riskId;

    /**
     * @param CustomerId $customerId
     * @param int $genderId
     * @param FirstName $firstName
     * @param LastName $lastName
     * @param Email $email
     * @param Birthday $birthday
     * @param bool $isEnabled
     * @param bool $isPartnerOffersSubscribed
     * @param bool $isNewsletterSubscribed
     * @param int[] $groupIds
     * @param int $defaultGroupId
     * @param string $companyName
     * @param string $siretCode
     * @param string $apeCode
     * @param string $website
     * @param float $allowedOutstandingAmount
     * @param int $maxPaymentDays
     * @param int $riskId
     */
    public function __construct(
        CustomerId $customerId,
        $genderId,
        FirstName $firstName,
        LastName $lastName,
        Email $email,
        Birthday $birthday,
        $isEnabled,
        $isPartnerOffersSubscribed,
        $isNewsletterSubscribed,
        array $groupIds,
        $defaultGroupId,
        $companyName,
        $siretCode,
        $apeCode,
        $website,
        $allowedOutstandingAmount,
        $maxPaymentDays,
        $riskId
    ) {
        $this->customerId = $customerId;
        $this->genderId = $genderId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->birthday = $birthday;
        $this->isEnabled = $isEnabled;
        $this->isPartnerOffersSubscribed = $isPartnerOffersSubscribed;
        $this->isNewsletterSubscribed = $isNewsletterSubscribed;
        $this->groupIds = $groupIds;
        $this->defaultGroupId = $defaultGroupId;
        $this->companyName = $companyName;
        $this->siretCode = $siretCode;
        $this->apeCode = $apeCode;
        $this->website = $website;
        $this->allowedOutstandingAmount = $allowedOutstandingAmount;
        $this->maxPaymentDays = $maxPaymentDays;
        $this->riskId = $riskId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return int
     */
    public function getGenderId()
    {
        return $this->genderId;
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
     * @return Birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return bool
     */
    public function isPartnerOffersSubscribed()
    {
        return $this->isPartnerOffersSubscribed;
    }

    /**
     * @return array|int[]
     */
    public function getGroupIds()
    {
        return $this->groupIds;
    }

    /**
     * @return int
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return string
     */
    public function getSiretCode()
    {
        return $this->siretCode;
    }

    /**
     * @return string
     */
    public function getApeCode()
    {
        return $this->apeCode;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @return float
     */
    public function getAllowedOutstandingAmount()
    {
        return $this->allowedOutstandingAmount;
    }

    /**
     * @return int
     */
    public function getMaxPaymentDays()
    {
        return $this->maxPaymentDays;
    }

    /**
     * @return int
     */
    public function getRiskId()
    {
        return $this->riskId;
    }

    /**
     * @return bool
     */
    public function isNewsletterSubscribed()
    {
        return $this->isNewsletterSubscribed;
    }
}
