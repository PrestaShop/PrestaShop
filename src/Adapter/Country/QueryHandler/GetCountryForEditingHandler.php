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

use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler\GetCountryForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryForEditing;

/**
 * Handles editable country query
 */
#[AsQueryHandler]
class GetCountryForEditingHandler implements GetCountryForEditingHandlerInterface
{
    /**
     * @var CountryRepository
     */
    private $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCountryForEditing $command): CountryForEditing
    {
        $countryId = $command->getCountryId();
        $country = $this->countryRepository->get($countryId);

        /*
         * todo: need to refacttor format when its part is merged,
         * to add: a dedicated service to fetch the country address format, it should implement a dedicated interface
         * for now the string format is enough, but maybe we should introduce some kind of DTO or maybe even a smarter Domain object that stores this address format
         * https://github.com/PrestaShop/PrestaShop/pull/29591#discussion_r976471671
         */
//        $format = AddressFormat::getAddressCountryFormat($countryId->getValue());

        return new CountryForEditing(
            $command->getCountryId(),
            $country->name,
            (string) $country->iso_code,
            (int) $country->call_prefix,
            (int) $country->id_currency,
            (int) $country->id_zone,
            (bool) $country->need_zip_code,
            (string) $country->zip_code_format,
            '', //todo: add when address format will be added
            (bool) $country->active,
            (bool) $country->contains_states,
            (bool) $country->need_identification_number,
            (bool) $country->display_tax_label,
            $country->getAssociatedShops()
        );
    }
}
