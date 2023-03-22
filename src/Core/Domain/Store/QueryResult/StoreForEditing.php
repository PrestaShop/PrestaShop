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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Store information for editing
 */
class StoreForEditing
{
    /**
     * @var StoreId
     */
    private $storeId;

    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @var StateId
     */
    private $stateId;

    /**
     * @var array|string[]
     */
    private $localisedNames;

    /**
     * @var array|string[]
     */
    private $localisedAddresses1;

    /**
     * @var array|string[]
     */
    private $localisedAddresses2;

    /**
     * @var string
     */
    private $postcode;

    /**
     * @var string
     */
    private $city;

    /**
    * @var float
    * */
    private $latitude;

    /**
    * @var float
    * */
    private $longitude;

    /**
     * @var string
     */
    private $hours;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var array|string[]
     */
    private $localisedNotes;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var int[]
     */
    private $shopAssociation;

    /**s
     * @var StoreId $storeId
     * @param CountryId $countryId
     * @param StateId $stateId
     * @param array|string[] $localisedNames
     * @param array|string[] $localisedAddresses1
     * @param array|string[] $localisedAddresses2
     * @param string $postcode
     * @param string $city
     * @param float $latitude
     * @param float $longitude
     * @param string  $hours
     * @param string $phone
     * @param string $fax
     * @param array|string[] $localisedNotes
     * @param string $email
     * @param bool $active
     * @param array|int[] $shopAssociation
     *
     * @throws StoreException
     * @throws DomainConstraintException
     */
    public function __construct(
        StoreId $storeId,
        CountryId $countryId,
        StateId $stateId,
        array $localisedNames,
        array $localisedAddresses1,
        array $localisedAddresses2,
        string $postcode,
        string $city,
        float $latitude,
        float $longitude,
        string $hours,
        string $phone,
        string  $fax,
        array $localisedNotes,
        string $email,
        bool $active,
        array $shopAssociation
    ) {
        $this->storeId = $storeId;
        $this->countryId = $countryId;
        $this->localisedNames = $localisedNames;
        $this->localisedAddresses1 = $localisedAddresses1;
        $this->localisedAddresses2 = $localisedAddresses2;
        $this->postcode = $postcode;
        $this->city = $city;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->hours = $hours;
        $this->phone = $phone;
        $this->fax = $fax;
        $this->localisedNotes = $localisedNotes;
        $this->email = $email;
        $this->active = $active;
        $this->shopAssociation = $shopAssociation;
    }

    

    /**
     * Get the value of storeId
     * @return StoreId
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Get the value of countryId
     * @return CountryId
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Get the value of stateId
     * @return StateId
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Get the value of localisedNames
     * @return array
     */
    public function getLocalisedNames()
    {
        return $this->localisedNames;
    }

    /**
     * Get the value of localisedAddresses1
     * @return array
     */
    public function getLocalisedAddresses1()
    {
        return $this->localisedAddresses1;
    }

    /**
     * Get the value of localisedAddresses2
     * @return array
     */
    public function getLocalisedAddresses2()
    {
        return $this->localisedAddresses2;
    }

    /**
     * Get the value of postcode
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Get the value of city
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get the value of latitude
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Get the value of longitude
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Get the value of hours
     * @return string
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Get the value of phone
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Get the value of fax
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Get the value of localisedNotes
     * @return array
     */
    public function getLocalisedNotes()
    {
        return $this->localisedNotes;
    }

    /**
     * Get the value of email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the value of active
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }
}
