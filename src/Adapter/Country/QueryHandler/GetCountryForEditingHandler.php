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

namespace PrestaShop\PrestaShop\Adapter\Country\QueryHandler;

use AddressFormat;
use Country;
use PrestaShop\PrestaShop\Adapter\Country\AbstractCountryHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler\GetCountryForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\EditableCountry;

/**
 * Handles editable country query
 */
class GetCountryForEditingHandler extends AbstractCountryHandler implements GetCountryForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CountryNotFoundException
     */
    public function handle(GetCountryForEditing $command): EditableCountry
    {
        $countryId = $command->getCountryId();
        $country = $this->getCountry($countryId);

        $countryIdValue = $countryId->getValue();
        $format = AddressFormat::getAddressCountryFormat($countryIdValue);

        return new EditableCountry(
            $command->getCountryId(),
            $country->name,
            $country->iso_code,
            $country->call_prefix,
            $country->id_currency,
            $country->id_zone,
            $country->need_zip_code,
            $country->zip_code_format,
            $format,
            $country->active,
            $country->contains_states,
            $country->need_identification_number,
            $country->display_tax_label,
            $country->id_shop_list
        );
    }
}
