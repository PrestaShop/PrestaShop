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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

@trigger_error(
    sprintf(
        '%s is deprecated since version 1.7.7.5 and will be removed in the next major version. Use %s::%s instead.',
        OrderPreviewShippingDetails::class,
        OrderPreview::class,
        'getShippingAddressFormatted()'
    ),
    E_USER_DEPRECATED
);

/**
 * DTO for order shipping details
 *
 * @deprecated Since 1.7.7.5 and will be removed in the next major.
 */
class OrderPreviewShippingDetails
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
    private $address1;

    /**
     * @var string
     */
    private $address2;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string|null
     */
    private $carrierName;

    /**
     * @var string|null
     */
    private $trackingNumber;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string|null
     */
    private $stateName;

    /**
     * @var string|null
     */
    private $company;

    /**
     * @var string|null
     */
    private $vatNumber;

    /**
     * @var string|null
     */
    private $dni;

    /**
     * @var string|null
     */
    private $trackingUrl;

    /**
     * InvoiceDetails constructor.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string|null $company
     * @param string|null $vatNumber
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $postalCode
     * @param string|null $stateName
     * @param string $country
     * @param string $phone
     * @param string|null $carrierName
     * @param string|null $trackingNumber
     * @param string|null $dni
     * @param string|null $trackingUrl
     */
    public function __construct(
        string $firstName,
        string $lastName,
        ?string $company,
        ?string $vatNumber,
        string $address1,
        string $address2,
        string $city,
        string $postalCode,
        ?string $stateName,
        string $country,
        string $phone,
        ?string $carrierName,
        ?string $trackingNumber,
        ?string $dni = null,
        ?string $trackingUrl = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->city = $city;
        $this->country = $country;
        $this->phone = $phone;
        $this->carrierName = $carrierName;
        $this->trackingNumber = $trackingNumber;
        $this->postalCode = $postalCode;
        $this->stateName = $stateName;
        $this->company = $company;
        $this->vatNumber = $vatNumber;
        $this->dni = $dni;
        $this->trackingUrl = $trackingUrl;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @return string|null
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @return string
     */
    public function getAddress1(): string
    {
        return $this->address1;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @return string|null
     */
    public function getStateName(): ?string
    {
        return $this->stateName;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @return string|null
     */
    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    /**
     * @return string|null
     */
    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    /**
     * @return string|null
     */
    public function getDNI(): ?string
    {
        return $this->dni;
    }

    /**
     * @return string|null
     */
    public function getTrackingUrl(): ?string
    {
        return $this->trackingUrl;
    }
}
