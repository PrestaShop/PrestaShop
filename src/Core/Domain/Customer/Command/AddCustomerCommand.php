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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\ApeCode;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;

/**
 * Adds new customer with provided data
 */
class AddCustomerCommand
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
     * @var Password
     */
    private $password;

    /**
     * @var int
     */
    private $defaultGroupId;

    /**
     * @var int[]
     */
    private $groupIds;

    /**
     * @var int|null
     */
    private $genderId;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var bool
     */
    private $isPartnerOffersSubscribed;

    /**
     * @var Birthday
     */
    private $birthday;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var string|null Only for B2b customers
     */
    private $companyName;

    /**
     * @var string|null Only for B2b customers
     */
    private $siretCode;

    /**
     * @var ApeCode|null Only for B2b customers
     */
    private $apeCode;

    /**
     * @var string|null Only for B2b customers
     */
    private $website;

    /**
     * @var float|null Only for B2b customers
     */
    private $allowedOutstandingAmount;

    /**
     * @var int|null Only for B2b customers
     */
    private $maxPaymentDays;

    /**
     * @var int|null Only for B2b customers
     */
    private $riskId;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param int $defaultGroupId
     * @param int[] $groupIds
     * @param int $shopId
     * @param int|null $genderId
     * @param bool $isEnabled
     * @param bool $isPartnerOffersSubscribed
     * @param string|null $birthday
     */
    public function __construct(
        $firstName,
        $lastName,
        $email,
        $password,
        $defaultGroupId,
        array $groupIds,
        $shopId,
        $genderId = null,
        $isEnabled = true,
        $isPartnerOffersSubscribed = false,
        $birthday = null
    ) {
        $this->firstName = new FirstName($firstName);
        $this->lastName = new LastName($lastName);
        $this->email = new Email($email);
        $this->password = new Password($password);
        $this->defaultGroupId = $defaultGroupId;
        $this->groupIds = $groupIds;
        $this->shopId = $shopId;
        $this->genderId = $genderId;
        $this->isEnabled = $isEnabled;
        $this->isPartnerOffersSubscribed = $isPartnerOffersSubscribed;
        $this->birthday = null !== $birthday ? new Birthday($birthday) : Birthday::createEmpty();
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
     * @return Password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /**
     * @return int[]
     */
    public function getGroupIds()
    {
        return $this->groupIds;
    }

    /**
     * @return int|null
     */
    public function getGenderId()
    {
        return $this->genderId;
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
     * @return Birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
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
