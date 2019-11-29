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

use PrestaShop\PrestaShop\Adapter\Country\AbstractCountryHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryRequiredFields;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler\GetCountryRequiredFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryRequiredFields;

/**
 * Handles and provides country state requirements
 */
final class GetCountryRequiredFieldsHandler extends AbstractCountryHandler implements GetCountryRequiredFieldsHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CountryNotFoundException
     */
    public function handle(GetCountryRequiredFields $query): CountryRequiredFields
    {
        $country = $this->getCountry($query->getCountryId());

        return new CountryRequiredFields($country->contains_states, $country->need_identification_number);
    }
}
