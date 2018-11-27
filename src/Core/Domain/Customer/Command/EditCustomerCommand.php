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

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Edits provided customer
 */
class EditCustomerCommand
{
    /**
     * @var CustomerId
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
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
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
     * @var string|null
     */
    private $birthday;

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
     * @param CustomerId $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
     */
    public function setDefaultGroupId($defaultGroupId)
    {
        $this->defaultGroupId = $defaultGroupId;
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
     */
    public function setGroupIds($groupIds)
    {
        $this->groupIds = $groupIds;
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
     */
    public function setGenderId($genderId)
    {
        $this->genderId = $genderId;
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
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
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
     */
    public function setIsPartnerOffersSubscribed($isPartnerOffersSubscribed)
    {
        $this->isPartnerOffersSubscribed = $isPartnerOffersSubscribed;
    }

    /**
     * @return string|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param string|null $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }
}
