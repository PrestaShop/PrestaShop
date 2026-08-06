<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetRequiredFieldsForAddress;

/**
 * Pure Core implementation of {@see AddressFormatFieldsProviderInterface}.
 *
 * Field lists per object are hardcoded here — they define the merchant-facing
 * picker surface for the country address-format builder, not a runtime reflection
 * of ObjectModel public properties. Decoupling them from legacy class shapes
 * keeps the picker stable and lets us drop the legacy AddressFormat dependency.
 *
 * Required-fields are sourced from the existing GetRequiredFieldsForAddress
 * query (DB-managed list) merged with a static minimum that the legacy
 * validator also enforced regardless of merchant configuration.
 */
final class AddressFormatFieldsProvider implements AddressFormatFieldsProviderInterface
{
    /**
     * Address fields that are always required on top of whatever the merchant
     * configures via BO > Customers > Addresses. Mirrors the legacy
     * AddressFormat::$requireFormFieldsList minimum.
     */
    private const STATIC_REQUIRED_FIELDS = [
        'firstname',
        'lastname',
        'address1',
        'city',
        'Country:name',
    ];

    /**
     * @var array<string, list<string>>
     */
    private const FIELDS_BY_CLASS = [
        'Address' => [
            'firstname',
            'lastname',
            'company',
            'vat_number',
            'address1',
            'address2',
            'postcode',
            'city',
            'other',
            'phone',
            'phone_mobile',
            'dni',
        ],
        'Country' => [
            'name',
            'iso_code',
        ],
        'State' => [
            'name',
            'iso_code',
        ],
        'Customer' => [
            'firstname',
            'lastname',
            'company',
            'vat_number',
            'email',
            'birthday',
            'website',
            'siret',
        ],
        'Warehouse' => [
            'name',
            'reference',
            'management_type',
        ],
    ];

    public function __construct(private readonly CommandBusInterface $queryBus)
    {
    }

    public function getPickerClasses(): array
    {
        return array_keys(self::FIELDS_BY_CLASS);
    }

    public function getFieldsForClass(string $className): array
    {
        return self::FIELDS_BY_CLASS[$className] ?? [];
    }

    public function getRequiredFields(): array
    {
        /** @var list<string> $dbManaged */
        $dbManaged = $this->queryBus->handle(new GetRequiredFieldsForAddress());

        return array_values(array_unique(array_merge(self::STATIC_REQUIRED_FIELDS, $dbManaged)));
    }
}
