<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Address\QueryHandler;

use AddressFormat;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetAddressFieldsForCountry;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryHandler\GetAddressFormatFieldsHandlerInterface;

final class GetAddressFieldsForCountryHandler implements GetAddressFormatFieldsHandlerInterface
{
    public function handle(GetAddressFieldsForCountry $query): array
    {
        $countryId = $query->getCountryId()->getValue();
//@todo: clean up.
        $adr_fields = AddressFormat::getOrderedAddressFields($countryId, true, true);

        $all_fields = [];
        $out = [];

        foreach ($adr_fields as $fields_line) {
            foreach (explode(' ', $fields_line) as $field_item) {
                $all_fields[] = trim($field_item);
            }
        }

        foreach (['inv', 'dlv'] as $adr_type) {
            $out[$adr_type . '_adr_fields'] = $adr_fields;
            $out[$adr_type . '_all_fields'] = $all_fields;
        }

        return array_unique(array_merge($out['dlv_adr_fields'], AddressFormat::$requireFormFieldsList));
    }
}
