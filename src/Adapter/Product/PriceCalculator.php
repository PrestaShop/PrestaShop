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

namespace PrestaShop\PrestaShop\Adapter\Product;

use Context;
use Product;

/**
 * Access Product price depending on taxes, eco tax, reductions.
 */
class PriceCalculator
{
    /**
     * @param int $idProduct Product id
     * @param bool $useTax With taxes or not (optional)
     * @param int|null $idProductAttribute product attribute id (optional).
     *                                     If set to false, do not apply the combination price impact.
     *                                     NULL does apply the default combination price impact
     * @param int $decimals Number of decimals (optional)
     * @param int|null $divisor Useful when paying many time without fees (optional)
     * @param bool $onlyReduc Returns only the reduction amount
     * @param bool $useReduc Set if the returned amount will include reduction
     * @param int $quantity Required for quantity discount application (default value: 1)
     * @param bool $forceAssociatedTax DEPRECATED - NOT USED Force to apply the associated tax.
     *                                 Only works when the parameter $usetax is true
     * @param int|null $idCustomer Customer ID (for customer group reduction)
     * @param int|null $idCart Cart ID. Required when the cookie is not accessible
     *                         (e.g., inside a payment module, a cron task...)
     * @param int|null $idAddress Customer address ID. Required for price (tax included)
     *                            calculation regarding the guest localization
     * @param null $specificPriceOutput If a specific price applies regarding the previous parameters,
     *                                  this variable is filled with the corresponding SpecificPrice object
     * @param bool $withEcotax insert ecotax in price output
     * @param bool $useGroupReduction
     * @param Context|null $context
     * @param bool $useCustomerPrice
     * @param int|null $idCustomization
     *
     * @return float Product price
     */
    public function getProductPrice(
        $idProduct,
        $useTax = true,
        $idProductAttribute = null,
        $decimals = 6,
        $divisor = null,
        $onlyReduc = false,
        $useReduc = true,
        $quantity = 1,
        $forceAssociatedTax = false,
        $idCustomer = null,
        $idCart = null,
        $idAddress = null,
        &$specificPriceOutput = null,
        $withEcotax = true,
        $useGroupReduction = true,
        Context $context = null,
        $useCustomerPrice = true,
        $idCustomization = null
    ) {
        return Product::getPriceStatic(
            $idProduct,
            $useTax,
            $idProductAttribute,
            $decimals,
            $divisor,
            $onlyReduc,
            $useReduc,
            $quantity,
            $forceAssociatedTax,
            $idCustomer,
            $idCart,
            $idAddress,
            $specificPriceOutput,
            $withEcotax,
            $useGroupReduction,
            $context,
            $useCustomerPrice,
            $idCustomization
        );
    }

    /**
     * Price calculation / Get product price.
     *
     * @param int $idShop Shop id
     * @param int $idProduct Product id
     * @param int $idProductAttribute Product attribute id
     * @param int $idCountry Country id
     * @param int $idState State id
     * @param string $zipCode
     * @param int $idCurrency Currency id
     * @param int $idGroup Group id
     * @param int $quantity Quantity Required for Specific prices : quantity discount application
     * @param bool $useTax with (1) or without (0) tax
     * @param int $decimals Number of decimals returned
     * @param bool $onlyReduc Returns only the reduction amount
     * @param bool $useReduc Set if the returned amount will include reduction
     * @param bool $withEcotax insert ecotax in price output
     * @param null $specificPrice If a specific price applies regarding the previous parameters,
     *                            this variable is filled with the corresponding SpecificPrice object
     * @param bool $useGroupReduction
     * @param int $idCustomer
     * @param bool $useCustomerPrice
     * @param int $idCart
     * @param int $realQuantity
     * @param int $idCustomization
     *
     * @return float Product price
     **/
    public function priceCalculation(
        $idShop,
        $idProduct,
        $idProductAttribute,
        $idCountry,
        $idState,
        $zipCode,
        $idCurrency,
        $idGroup,
        $quantity,
        $useTax,
        $decimals,
        $onlyReduc,
        $useReduc,
        $withEcotax,
        &$specificPrice,
        $useGroupReduction,
        $idCustomer = 0,
        $useCustomerPrice = true,
        $idCart = 0,
        $realQuantity = 0,
        $idCustomization = 0
    ) {
        return Product::priceCalculation(
            $idShop,
            $idProduct,
            $idProductAttribute,
            $idCountry,
            $idState,
            $zipCode,
            $idCurrency,
            $idGroup,
            $quantity,
            $useTax,
            $decimals,
            $onlyReduc,
            $useReduc,
            $withEcotax,
            $specificPrice,
            $useGroupReduction,
            $idCustomer,
            $useCustomerPrice,
            $idCart,
            $realQuantity,
            $idCustomization
        );
    }

    /**
     * @param int $orderId Order id
     * @param int $productId Product id
     * @param int $combinationId Combination (Product attribute) id
     * @param bool $withTaxes With or without taxes
     * @param bool $useReduction With or without reduction
     * @param bool $withEcoTax With or without ecto tax
     *
     * @return float|null
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getOrderPrice(
        int $orderId,
        int $productId,
        int $combinationId,
        bool $withTaxes,
        bool $useReduction,
        bool $withEcoTax
    ): ?float {
        return Product::getPriceFromOrder(
            $orderId,
            $productId,
            $combinationId,
            $withTaxes,
            $useReduction,
            $withEcoTax
        );
    }
}
