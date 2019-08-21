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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetAddressLayoutFields;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\AddressLayoutFields;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\EditableCountry;

/**
 * Provides data for country add/edit form
 */
final class CountryFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $isMultistoreFeatureActive
     */
    public function __construct(CommandBusInterface $queryBus, bool $isMultistoreFeatureActive)
    {
        $this->queryBus = $queryBus;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     */
    public function getData($countryId)
    {
        /** @var EditableCountry $editableCountry */
        $editableCountry = $this->queryBus->handle(new GetCountryForEditing((int) $countryId));

        $data = [
            'country' => $editableCountry->getLocalisedNames(),
            'iso_code' => $editableCountry->getIsoCode(),
            'call_prefix' => $editableCountry->getCallPrefix(),
            'default_currency' => $editableCountry->getDefaultCurrency(),
            'zone' => $editableCountry->getZone(),
            'need_zip_code' => $editableCountry->needZipCode(),
            'zip_code_format' => $editableCountry->getZipCodeFormat(),
            'address_format' => $editableCountry->getAddressFormat(),
            'is_enabled' => $editableCountry->isEnabled(),
            'contains_states' => $editableCountry->containsStates(),
            'need_identification_number' => $editableCountry->needIdNumber(),
            'display_tax_label' => $editableCountry->displayTaxLabel(),
        ];

        if ($this->isMultistoreFeatureActive) {
            $data['shop_association'] = $editableCountry->getShopAssociation();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        /** @var AddressLayoutFields $addressLayout */
        $addressLayout = $this->queryBus->handle(new GetAddressLayoutFields());

        return [
            'address_format' => $addressLayout->getDefaultLayout(),
        ];
    }
}
