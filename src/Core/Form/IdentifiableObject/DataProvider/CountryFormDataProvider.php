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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryForEditing;

/**
 * Provides data for zone add/edit form.
 */
class CountryFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    protected $queryBus;

    /**
     * @var bool
     */
    protected $multistoreEnabled;

    /**
     * @var int[]
     */
    protected $defaultShopAssociation;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $multistoreEnabled
     * @param array $defaultShopAssociation
     */
    public function __construct(
        CommandBusInterface $queryBus,
        bool $multistoreEnabled,
        array $defaultShopAssociation
    ) {
        $this->queryBus = $queryBus;
        $this->multistoreEnabled = $multistoreEnabled;
        $this->defaultShopAssociation = $defaultShopAssociation;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        /** @var CountryForEditing $editableCountry */
        $editableCountry = $this->queryBus->handle(new GetCountryForEditing($id));

        $data = [
            'name' => $editableCountry->getLocalizedNames(),
            'iso_code' => $editableCountry->getIsoCode(),
            'call_prefix' => $editableCountry->getCallPrefix(),
            'default_currency' => $editableCountry->getDefaultCurrency(),
            'zone' => $editableCountry->getZone(),
            'need_zip_code' => $editableCountry->isNeedZipCode(),
            'zip_code_format' => null !== $editableCountry->getZipCodeFormat() ? $editableCountry->getZipCodeFormat()->getValue() : null,
            'address_format' => $editableCountry->getAddressFormat(),
            'is_enabled' => $editableCountry->isEnabled(),
            'contains_states' => $editableCountry->isContainsStates(),
            'need_identification_number' => $editableCountry->isNeedIdNumber(),
            'display_tax_label' => $editableCountry->isDisplayTaxLabel(),
        ];

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $editableCountry->getShopAssociation();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        $data = [
            'need_zip_code' => false,
            'is_enabled' => true,
            'contains_states' => false,
            'need_identification_number' => false,
            'display_tax_label' => true,
        ];

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
