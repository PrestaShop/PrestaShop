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

/**
 * Adds new customer with provided data
 */
class AddCustomerCommand
{
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
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $defaultGroupId;

    /**
     * @var array|int[]
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
     * @var string
     */
    private $birthday;

    /**
     * @var int
     */
    private $shopId;

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
     * @param string $birthday
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
        $birthday = '0000-00-00'
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->defaultGroupId = $defaultGroupId;
        $this->groupIds = $groupIds;
        $this->shopId = $shopId;
        $this->genderId = $genderId;
        $this->isEnabled = $isEnabled;
        $this->isPartnerOffersSubscribed = $isPartnerOffersSubscribed;
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
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
     * @return string
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
}
