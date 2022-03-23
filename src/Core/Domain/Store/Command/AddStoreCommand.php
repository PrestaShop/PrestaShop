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

namespace PrestaShop\PrestaShop\Core\Domain\Store\Command;

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
 * Class AddStoreCommand is responsible for adding store data.
 */
class AddStoreCommand
{
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
     * @param int $countryId
     * @param string $postcode
     * @param string $city
     * @param int[] $shopAssociation
     */
    public function __construct(int $countryId, string $postcode, string $city, $shopAssociation)
    {
        $this->setCountryId($countryId);
        $this->setPostcode($postcode);
        $this->setCity($city);
        $this->setShopAssociation($shopAssociation);
    }

    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }

    public function setCountryId(int $countryId): self
    {
        $this->countryId = new CountryId($countryId);

        return $this;
    }

    public function getStateId(): ?StateId
    {
        return $this->stateId;
    }

    public function setStateId(?int $stateId): self
    {
        if (null !== $stateId) {
            $stateId = new StateId($stateId);
        }

        $this->stateId = $stateId;

        return $this;
    }

    public function getPostcode(): Postcode
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = new Postcode($postcode);

        return $this;
    }

    public function getCity(): CityName
    {
        return $this->city;
    }

    public function setCity($city): self
    {
        $this->city = new CityName($city);

        return $this;
    }

    public function getLatitude(): ?Coordinate
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        if (null !== $latitude) {
            $latitude = new Coordinate($latitude);
        }

        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?Coordinate
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        if (null !== $longitude) {
            $longitude = new Coordinate($longitude);
        }

        $this->longitude = $longitude;

        return $this;
    }

    public function getPhone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        if (null !== $phone) {
            $phone = new PhoneNumber($phone);
        }

        $this->phone = $phone;

        return $this;
    }

    public function getFax(): ?PhoneNumber
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        if (null !== $fax) {
            $fax = new PhoneNumber($fax);
        }

        $this->fax = $fax;

        return $this;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        if (null !== $email) {
            $email = new Email($email);
        }

        $this->email = $email;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     */
    public function setShopAssociation(array $shopAssociation): self
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    /**
     * @return array<int, GenericName>
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @param array<int, string> $localizedNames
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        foreach ($localizedNames as $langId => $name) {
            $this->localizedNames[$langId] = new GenericName($name);
        }

        return $this;
    }

    /**
     * @return array<int, Address>
     */
    public function getLocalizedAddress1(): array
    {
        return $this->localizedAddress1;
    }

    /**
     * @param array<int, string> $localizedAddress1
     */
    public function setLocalizedAddress1(array $localizedAddress1): self
    {
        foreach ($localizedAddress1 as $langId => $address) {
            $this->localizedAddress1[$langId] = new Address($address);
        }

        return $this;
    }

    /**
     * @return array<int, Address>
     */
    public function getLocalizedAddress2(): array
    {
        return $this->localizedAddress2;
    }

    /**
     * @param array<int, string> $localizedAddress2
     */
    public function setLocalizedAddress2(array $localizedAddress2): self
    {
        foreach ($localizedAddress2 as $langId => $address) {
            $this->localizedAddress2[$langId] = new Address($address);
        }

        return $this;
    }

    /**
     * @return array<int, Hours>
     */
    public function getLocalizedHours(): array
    {
        return $this->localizedHours;
    }

    /**
     * @param array<int, string> $localizedHours
     */
    public function setLocalizedHours(array $localizedHours): self
    {
        foreach ($localizedHours as $langId => $hours) {
            $this->localizedHours[$langId] = new Hours($hours);
        }

        return $this;
    }

    /**
     * @return array<int, Note>
     */
    public function getLocalizedNotes(): array
    {
        return $this->localizedNotes;
    }

    /**
     * @param array<int, string> $localizedNotes
     */
    public function setLocalizedNotes(array $localizedNotes): self
    {
        foreach ($localizedNotes as $langId => $note) {
            $this->localizedNotes[$langId] = new Note($note);
        }

        return $this;
    }
}
