<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat;

/**
 * Lists the public properties of the ObjectModel classes that the address-format
 * picker exposes (Customer, Warehouse, Country, State, Address). Used by the form
 * type to populate the Vue builder's picker pills.
 */
interface AddressFormatFieldsProviderInterface
{
    /**
     * Returns the ordered list of object class names the picker exposes
     * (e.g. ['Address', 'Country', 'State', 'Customer', 'Warehouse']). This is the
     * single source of truth: the form type uses it to build the picker columns
     * and the validator uses it to reject prefixed tokens that target any other class.
     *
     * @return list<string>
     */
    public function getPickerClasses(): array;

    /**
     * @param string $className One of the picker's object names
     *
     * @return list<string>
     */
    public function getFieldsForClass(string $className): array;

    /**
     * Returns the merged list of required fields the address format must include:
     * the static defaults (firstname, lastname, address1, city, Country:name) plus
     * any field the merchant flagged as required in BO > Customers > Addresses.
     *
     * This is broader than the dedicated GetRequiredFieldsForAddress CQRS query,
     * which intentionally returns only the DB-managed list and is consumed by the
     * customer-facing address form. The address-format builder needs both layers
     * because the legacy validator does too (see AddressFormat::getFieldsRequired).
     *
     * @return list<string>
     */
    public function getRequiredFields(): array;
}
