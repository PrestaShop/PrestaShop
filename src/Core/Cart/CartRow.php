<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Cart;

use Cart;
use PrestaShop\PrestaShop\Adapter\AddressFactory;
use PrestaShop\PrestaShop\Adapter\Cache\CacheAdapter;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\Product\PriceCalculator;
use PrestaShop\PrestaShop\Adapter\Tools;

/**
 * represent a cart row, ie a product and a quantity, and some post-process data like cart rule applied
 */
class CartRow
{
    const ROUND_MODE_ITEM  = 'item';
    const ROUND_MODE_LINE  = 'line';
    const ROUND_MODE_TOTAL = 'total';

    /**
     * @var PriceCalculator
     */
    protected $priceCalculator;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * @var CustomerDataProvider
     */
    protected $customerDataProvider;

    /**
     * @var GroupDataProvider
     */
    protected $groupDataProvider;

    /**
     * @var Database
     */
    protected $databaseAdapter;

    /**
     * @var CacheAdapter
     */
    protected $cacheAdapter;

    protected $useEcotax;
    protected $precision;
    protected $roundType;

    /**
     * @var array previous data for product: array given by Cart::getProducts()
     */
    protected $rowData = [];

    /**
     * @var AmountImmutable
     */
    protected $initialUnitPrice;

    /**
     * @var AmountImmutable
     */
    protected $finalUnitPrice;

    /**
     * @var AmountImmutable
     */
    protected $finalTotalPrice;

    /**
     * @var bool indicates if the calculation was triggered (reset on data changes)
     */
    protected $isProcessed = false;

    /**
     * @param array                $rowData array item given by Cart::getProducts()
     * @param PriceCalculator      $priceCalculator
     * @param AddressFactory       $addressFactory
     * @param CustomerDataProvider $customerDataProvider
     * @param CacheAdapter         $cacheAdapter
     * @param GroupDataProvider    $groupDataProvider
     * @param Database             $databaseAdapter
     * @param bool                 $useEcotax
     * @param                      $precision
     * @param                      $roundType
     */
    public function __construct(
        $rowData,
        PriceCalculator $priceCalculator,
        AddressFactory $addressFactory,
        CustomerDataProvider $customerDataProvider,
        CacheAdapter $cacheAdapter,
        GroupDataProvider $groupDataProvider,
        Database $databaseAdapter,
        $useEcotax,
        $precision,
        $roundType
    ) {
        $this->setRowData($rowData);
        $this->priceCalculator      = $priceCalculator;
        $this->addressFactory       = $addressFactory;
        $this->customerDataProvider = $customerDataProvider;
        $this->cacheAdapter         = $cacheAdapter;
        $this->groupDataProvider    = $groupDataProvider;
        $this->databaseAdapter      = $databaseAdapter;
        $this->useEcotax            = $useEcotax;
        $this->precision            = $precision;
        $this->roundType            = $roundType;
    }

    /**
     * @param array $rowData
     *
     * @return CartRow
     */
    public function setRowData($rowData)
    {
        $this->rowData = $rowData;

        return $this;
    }

    /**
     * @return array
     */
    public function getRowData()
    {
        return $this->rowData;
    }

    /**
     * @return \PrestaShop\PrestaShop\Core\Cart\AmountImmutable
     * @throws \Exception
     */
    public function getInitialUnitPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->initialUnitPrice;
    }

    /**
     * return final price: initial minus the cart rule discounts
     *
     * @return \PrestaShop\PrestaShop\Core\Cart\AmountImmutable
     * @throws \Exception
     */
    public function getFinalUnitPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->finalUnitPrice;
    }

    /**
     * return final price: initial minus the cart rule discounts
     *
     * @return \PrestaShop\PrestaShop\Core\Cart\AmountImmutable
     * @throws \Exception
     */
    public function getFinalTotalPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->finalTotalPrice;
    }

    /**
     * run initial row calculation
     *
     * @param Cart $cart
     *
     * @throws \PrestaShop\PrestaShop\Adapter\CoreException
     */
    public function processCalculation(Cart $cart)
    {
        $rowData                = $this->getRowData();
        $quantity               = (int) $rowData['cart_quantity'];
        $this->initialUnitPrice = $this->getProductPrice($cart, $rowData);
        // store not rounded values
        $this->finalTotalPrice = new AmountImmutable(
            $this->initialUnitPrice->getTaxIncluded() * $quantity,
            $this->initialUnitPrice->getTaxExcluded() * $quantity
        );
        $this->applyRound();
        // store state
        $this->isProcessed = true;
    }

    protected function getProductPrice(Cart $cart, $rowData)
    {
        $productId = (int) $rowData['id_product'];
        $quantity  = (int) $rowData['cart_quantity'];

        $addressId = $cart->getProductAddressId($rowData);
        if (!$addressId) {
            $addressId = $cart->getTaxAddressId();
        }
        $address   = $this->addressFactory->findOrCreate($addressId, true);
        $countryId = (int) $address->id_country;
        $stateId   = (int) $address->id_state;
        $zipCode   = $address->postcode;

        $shopId     = (int) $cart->id_shop;
        $currencyId = (int) $cart->id_currency;

        $groupId = null;
        if ($cart->id_customer) {
            $groupId = $this->customerDataProvider->getDefaultGroupId((int) $cart->id_customer);
        }
        if (!$groupId) {
            $groupId = (int) $this->groupDataProvider->getCurrent()->id;
        }

        $cartQuantity = 0;
        if ((int) $cart->id) {
            $cacheId = 'Product::getPriceStatic_' . (int) $productId . '-' . (int) $cart->id;
            if (!$this->cacheAdapter->isStored($cacheId)
                || ($cartQuantity = $this->cacheAdapter->retrieve($cacheId)
                                    != (int) $quantity)) {
                $sql          = 'SELECT SUM(`quantity`)
				FROM `' . _DB_PREFIX_ . 'cart_product`
				WHERE `id_product` = ' . (int) $productId . '
				AND `id_cart` = ' . (int) $cart->id;
                $cartQuantity = (int) $this->databaseAdapter->getValue(_PS_USE_SQL_SLAVE_, $sql);
                $this->cacheAdapter->store($cacheId, $cartQuantity);
            } else {
                $cartQuantity = $this->cacheAdapter->retrieve($cacheId);
            }
        }

        // The $null variable below is not used,
        // but it is necessary to pass it to getProductPrice because
        // it expects a reference.
        $specificPriceOutput = null;

        $priceTaxIncl = $this->priceCalculator->priceCalculation(
            $shopId,
            (int) $productId,
            (int) $rowData['id_product_attribute'],
            $countryId,
            $stateId,
            $zipCode,
            $currencyId,
            $groupId,
            $quantity,
            true,
            6,
            false,
            true,
            $this->useEcotax,
            $specificPriceOutput,
            true,
            (int) $cart->id_customer ? (int) $cart->id_customer : null,
            true,
            (int) $cart->id,
            $cartQuantity,
            (int) $rowData['id_customization']
        );
        $priceTaxExcl = $this->priceCalculator->priceCalculation(
            $shopId,
            (int) $productId,
            (int) $rowData['id_product_attribute'],
            $countryId,
            $stateId,
            $zipCode,
            $currencyId,
            $groupId,
            $quantity,
            false,
            6,
            false,
            true,
            $this->useEcotax,
            $specificPriceOutput,
            true,
            (int) $cart->id_customer ? (int) $cart->id_customer : null,
            true,
            (int) $cart->id,
            $cartQuantity,
            (int) $rowData['id_customization']
        );

        return new AmountImmutable($priceTaxIncl, $priceTaxExcl);
    }

    protected function applyRound()
    {
        // ROUNDING MODE
        $this->finalUnitPrice = clone($this->initialUnitPrice);

        $rowData  = $this->getRowData();
        $quantity = (int) $rowData['cart_quantity'];
        $tools    = new Tools;
        switch ($this->roundType) {
            case self::ROUND_MODE_TOTAL:
                // do not round the line
                $this->finalTotalPrice = new AmountImmutable(
                    $this->initialUnitPrice->getTaxIncluded() * $quantity,
                    $this->initialUnitPrice->getTaxExcluded() * $quantity
                );
                break;
            case self::ROUND_MODE_LINE:
                // round line result
                $this->finalTotalPrice = new AmountImmutable(
                    $tools->round($this->initialUnitPrice->getTaxIncluded() * $quantity, $this->precision),
                    $tools->round($this->initialUnitPrice->getTaxExcluded() * $quantity, $this->precision)
                );
                break;

            case self::ROUND_MODE_ITEM:
            default:
                // round each item
                $this->initialUnitPrice = new AmountImmutable(
                    $tools->round($this->initialUnitPrice->getTaxIncluded(), $this->precision),
                    $tools->round($this->initialUnitPrice->getTaxExcluded(), $this->precision)
                );
                $this->finalTotalPrice  = new AmountImmutable(
                    $this->initialUnitPrice->getTaxIncluded() * $quantity,
                    $this->initialUnitPrice->getTaxExcluded() * $quantity
                );
                break;
        }
    }

    /**
     * substract discount from the row
     * if discount exceeds amount, we keep 0 (no use of negative amounts)
     *
     * @param \PrestaShop\PrestaShop\Core\Cart\AmountImmutable $amount
     */
    public function applyFlatDiscount(AmountImmutable $amount)
    {
        $taxIncluded = $this->finalTotalPrice->getTaxIncluded() - $amount->getTaxIncluded();
        $taxExcluded = $this->finalTotalPrice->getTaxExcluded() - $amount->getTaxExcluded();
        if ($taxIncluded < 0) {
            $taxIncluded = 0;
        }
        if ($taxExcluded < 0) {
            $taxExcluded = 0;
        }
        $this->finalTotalPrice = new AmountImmutable(
            $taxIncluded,
            $taxExcluded
        );

        $this->updateFinalUnitPrice();
    }

    /**
     * @param float $percent 0-100
     *
     * @return AmountImmutable
     */
    public function applyPercentageDiscount($percent)
    {
        $percent = (float) $percent;
        if ($percent < 0 || $percent > 100) {
            throw new \Exception('Invalid percentage discount given: ' . $percent);
        }
        $discountTaxIncluded = $this->finalTotalPrice->getTaxIncluded() * $percent / 100;
        $discountTaxExcluded = $this->finalTotalPrice->getTaxExcluded() * $percent / 100;
        $amount              = new AmountImmutable($discountTaxIncluded, $discountTaxExcluded);
        $this->applyFlatDiscount($amount);

        return $amount;
    }

    /**
     * when final row price is calculated, we need to update unit price
     */
    protected function updateFinalUnitPrice()
    {
        $rowData              = $this->getRowData();
        $quantity             = (int) $rowData['cart_quantity'];
        $taxIncluded          = $this->finalTotalPrice->getTaxIncluded();
        $taxExcluded          = $this->finalTotalPrice->getTaxExcluded();
        $this->finalUnitPrice = new AmountImmutable(
            $taxIncluded / $quantity,
            $taxExcluded / $quantity
        );
    }
}
