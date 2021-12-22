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

namespace PrestaShop\PrestaShop\Adapter\Tax;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

class TaxComputer
{
    /**
     * @var TaxRulesGroupRepository
     */
    private $taxRulesGroupRepository;

    /**
     * @param TaxRulesGroupRepository $taxRulesGroupRepository
     */
    public function __construct(
        TaxRulesGroupRepository $taxRulesGroupRepository
    ) {
        $this->taxRulesGroupRepository = $taxRulesGroupRepository;
    }

    /**
     * @param DecimalNumber $priceTaxExcluded
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     *
     * @return DecimalNumber
     */
    public function computePriceWithTaxes(
        DecimalNumber $priceTaxExcluded,
        TaxRulesGroupId $taxRulesGroupId,
        CountryId $countryId
    ): DecimalNumber {
        $taxRatio = $this->getTaxRatio($taxRulesGroupId, $countryId);

        return $priceTaxExcluded->times($taxRatio);
    }

    /**
     * @param DecimalNumber $priceTaxIncluded
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     *
     * @return DecimalNumber
     */
    public function computePriceWithoutTaxes(
        DecimalNumber $priceTaxIncluded,
        TaxRulesGroupId $taxRulesGroupId,
        CountryId $countryId
    ): DecimalNumber {
        $taxRatio = $this->getTaxRatio($taxRulesGroupId, $countryId);

        return $priceTaxIncluded->dividedBy($taxRatio);
    }

    /**
     * @param TaxRulesGroupId $taxRulesGroupId
     * @param CountryId $countryId
     *
     * @return DecimalNumber
     */
    private function getTaxRatio(TaxRulesGroupId $taxRulesGroupId, CountryId $countryId): DecimalNumber
    {
        $taxRulesGroup = $this->taxRulesGroupRepository->getTaxRulesGroupDetails($taxRulesGroupId);
        if (!empty($taxRulesGroup['rates'])) {
            // Use the tax rate associated to context country, or the first one as fallback
            $countryTaxRate = $taxRulesGroup['rates'][$countryId->getValue()] ?? reset($taxRulesGroup['rates']);
        } else {
            $countryTaxRate = 0;
        }

        return new DecimalNumber((string) (1 + ($countryTaxRate / 100)));
    }
}
