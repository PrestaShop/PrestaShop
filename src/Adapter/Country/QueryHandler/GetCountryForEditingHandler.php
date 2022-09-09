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

namespace PrestaShop\PrestaShop\Adapter\Country\QueryHandler;

use AddressFormat;
use Country;
use PrestaShop\PrestaShop\Adapter\Country\AbstractCountryHandler;
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
     */
    public function handle(GetCountryForEditing $command): EditableCountry
    {
        $countryId = $command->getCountryId();
        $country = $this->getCountry($countryId);

        $format = AddressFormat::getAddressCountryFormat($countryId->getValue());

        return new EditableCountry(
            $command->getCountryId(),
            $country->name,
            (string) $country->iso_code,
            (int) $country->call_prefix,
            (int) $country->id_currency,
            (int) $country->id_zone,
            (bool) $country->need_zip_code,
            (string) $country->zip_code_format,
            (string) $format,
            (bool) $country->active,
            (bool) $country->contains_states,
            (bool) $country->need_identification_number,
            (bool) $country->display_tax_label,
            $country->getAssociatedShops()
        );
    }
}
