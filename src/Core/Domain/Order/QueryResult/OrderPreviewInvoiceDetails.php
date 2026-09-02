<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

@trigger_error(
    sprintf(
        '%s is deprecated since version 1.7.7.5 and will be removed in the next major version. Use %s::%s instead.',
        OrderPreviewShippingDetails::class,
        OrderPreview::class,
        'getInvoiceAddressFormatted()'
    ),
    E_USER_DEPRECATED
);

/**
 * DTO for order invoice details
 *
 * @deprecated Since 1.7.7.5 and will be removed in the next major.
 */
class OrderPreviewInvoiceDetails
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
    private $email;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string|null
     */
    private $vatNumber;

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
    private $dni;

    /**
     * InvoiceDetails constructor.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $company
     * @param string|null $vatNumber
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $postalCode
     * @param string|null $stateName
     * @param string $country
     * @param string|null $email
     * @param string $phone
     * @param string|null $dni
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
        ?string $email,
        string $phone,
        ?string $dni = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->city = $city;
        $this->country = $country;
        $this->email = $email;
        $this->phone = $phone;
        $this->company = $company;
        $this->vatNumber = $vatNumber;
        $this->postalCode = $postalCode;
        $this->stateName = $stateName;
        $this->dni = $dni;
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
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
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
     * @return string
     */
    public function getCompany(): string
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
     * @return string|null
     */
    public function getDNI(): ?string
    {
        return $this->dni;
    }
}
