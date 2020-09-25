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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class PersonalInformation holds personal customer information.
 */
class PersonalInformation
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
     * @var bool
     */
    private $isGuest;

    /**
     * @var string
     */
    private $socialTitle;

    /**
     * @var string
     */
    private $birthday;

    /**
     * @var string
     */
    private $registrationDate;

    /**
     * @var string
     */
    private $lastUpdateDate;

    /**
     * @var string
     */
    private $lastVisitDate;

    /**
     * @var string
     */
    private $rankBySales;

    /**
     * @var string
     */
    private $shopName;

    /**
     * @var string
     */
    private $languageName;

    /**
     * @var Subscriptions
     */
    private $subscriptions;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param bool $isGuest
     * @param string $socialTitle
     * @param string $birthday
     * @param string $registrationDate
     * @param string $lastUpdateDate
     * @param string $lastVisitDate
     * @param string $rankBySales
     * @param string $shopName
     * @param string $languageName
     * @param Subscriptions $subscriptions
     * @param bool $isActive
     */
    public function __construct(
        $firstName,
        $lastName,
        $email,
        $isGuest,
        $socialTitle,
        $birthday,
        $registrationDate,
        $lastUpdateDate,
        $lastVisitDate,
        $rankBySales,
        $shopName,
        $languageName,
        Subscriptions $subscriptions,
        $isActive
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->isGuest = $isGuest;
        $this->socialTitle = $socialTitle;
        $this->birthday = $birthday;
        $this->registrationDate = $registrationDate;
        $this->lastUpdateDate = $lastUpdateDate;
        $this->lastVisitDate = $lastVisitDate;
        $this->rankBySales = $rankBySales;
        $this->shopName = $shopName;
        $this->languageName = $languageName;
        $this->subscriptions = $subscriptions;
        $this->isActive = $isActive;
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
     * @return bool
     */
    public function isGuest()
    {
        return $this->isGuest;
    }

    /**
     * @return string
     */
    public function getSocialTitle()
    {
        return $this->socialTitle;
    }

    /**
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @return string
     */
    public function getLastUpdateDate()
    {
        return $this->lastUpdateDate;
    }

    /**
     * @return string
     */
    public function getLastVisitDate()
    {
        return $this->lastVisitDate;
    }

    /**
     * @return string
     */
    public function getRankBySales()
    {
        return $this->rankBySales;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * @return string
     */
    public function getLanguageName()
    {
        return $this->languageName;
    }

    /**
     * @return Subscriptions
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }
}
