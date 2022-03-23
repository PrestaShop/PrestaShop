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

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\Coordinate;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\Hours;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Address;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\CityName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\GenericName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Note;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\PhoneNumber;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Postcode;

/**
 * Store information for editing
 */
class StoreForEditing
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @var StateId|null
     */
    private $stateId;

    /**
     * @var Postcode
     */
    private $postcode;

    /**
     * @var CityName
     */
    private $city;

    /**
     * @var Coordinate|null
     */
    private $latitude;

    /**
     * @var Coordinate|null
     */
    private $longitude;

    /**
     * @var PhoneNumber|null
     */
    private $phone;

    /**
     * @var PhoneNumber|null
     */
    private $fax;

    /**
     * @var Email|null
     */
    private $email;

    /**
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @var array<int, GenericName>
     */
    private $localizedNames = [];

    /**
     * @var array<int, Address>
     */
    private $localizedAddress1 = [];

    /**
     * @var array<int, Address>
     */
    private $localizedAddress2 = [];

    /**
     * @var array<int, Hours>
     */
    private $localizedHours = [];

    /**
     * @var array<int, Note>
     */
    private $localizedNotes = [];

    /**
     * @param int $storeId
     * @param bool $isActive
     * @param int[] $shopAssociation
     * @param array<int, string> $localizedNames
     * @param array<int, string> $localizedAddress1
     * @param array<int, string> $localizedAddress2
     * @param array<int, string> $localizedHours
     * @param array<int, string> $localizedNotes
     */
    public function __construct(
        int $storeId,
        bool $isActive,
        $shopAssociation,
        int $countryId,
        string $postcode,
        string $city,
        ?int $stateId = null,
        ?string $latitude = null,
        ?string $longitude = null,
        ?string $phone = null,
        ?string $fax = null,
        ?string $email = null,
        $localizedNames = [],
        $localizedAddress1 = [],
        $localizedAddress2 = [],
        $localizedHours = [],
        $localizedNotes = []
    ) {
        $this->storeId = $storeId;
        $this->active = $isActive;
        $this->shopAssociation = $shopAssociation;
        $this->countryId = new CountryId($countryId);
        $this->postcode = new Postcode($postcode);
        $this->city = new CityName($city);
        $this->stateId = $stateId !== null ? new StateId($stateId) : null;
        $this->latitude = $latitude !== null ? new Coordinate($latitude) : null;
        $this->longitude = $longitude !== null ? new Coordinate($longitude) : null;
        $this->phone = $phone !== null ? new PhoneNumber($phone) : null;
        $this->fax = $fax !== null ? new PhoneNumber($fax) : null;
        $this->email = $email !== null ? new Email($email) : null;

        foreach ($localizedNames as $langId => $name) {
            $this->localizedNames[$langId] = new GenericName($name);
        }

        foreach ($localizedAddress1 as $langId => $address) {
            $this->localizedAddress1[$langId] = new Address($address);
        }

        foreach ($localizedAddress2 as $langId => $address) {
            $this->localizedAddress2[$langId] = new Address($address);
        }

        foreach ($localizedHours as $langId => $hours) {
            $this->localizedHours[$langId] = new Hours($hours);
        }

        foreach ($localizedNotes as $langId => $note) {
            $this->localizedNotes[$langId] = new Note($note);
        }
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->storeId;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return CountryId
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @return StateId|null
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * @return Postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @return CityName
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return Coordinate|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return Coordinate|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return PhoneNumber|null
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @return Email|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @return array<int, GenericName>
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return array<int, Address>
     */
    public function getLocalizedAddress1()
    {
        return $this->localizedAddress1;
    }

    /**
     * @return array<int, Address>
     */
    public function getLocalizedAddress2()
    {
        return $this->localizedAddress2;
    }

    /**
     * @return array<int, Hours>
     */
    public function getLocalizedHours()
    {
        return $this->localizedHours;
    }

    /**
     * @return array<int, Note>
     */
    public function getLocalizedNotes()
    {
        return $this->localizedNotes;
    }
}
