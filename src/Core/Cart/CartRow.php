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

namespace PrestaShop\PrestaShop\Core\Cart;

use Cart;
use PrestaShop\PrestaShop\Adapter\AddressFactory;
use PrestaShop\PrestaShop\Adapter\Cache\CacheAdapter;
use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\Product\PriceCalculator;
use PrestaShop\PrestaShop\Adapter\Tools;

/**
 * represent a cart row, ie a product and a quantity, and some post-process data like cart rule applied.
 */
class CartRow
{
    /**
     * row round mode by item.
     */
    public const ROUND_MODE_ITEM = 'item';

    /**
     * row round mode by line.
     */
    public const ROUND_MODE_LINE = 'line';

    /**
     * row round mode by all lines.
     */
    public const ROUND_MODE_TOTAL = 'total';

    /**
     * static cache key pattern.
     */
    public const PRODUCT_PRICE_CACHE_ID_PATTERN = 'Product::getPriceStatic_%d-%d';

    /**
     * @var PriceCalculator adapter to calculate price
     */
    protected $priceCalculator;

    /**
     * @var AddressFactory adapter to get address informations
     */
    protected $addressFactory;

    /**
     * @var CustomerDataProvider adapter to get customer informations
     */
    protected $customerDataProvider;

    /**
     * @var GroupDataProvider adapter to get group informations
     */
    protected $groupDataProvider;

    /**
     * @var Database adapter to get database
     */
    protected $databaseAdapter;

    /**
     * @var CacheAdapter adapter to get cache
     */
    protected $cacheAdapter;

    /**
     * @var bool calculation will use ecotax
     */
    protected $useEcotax;

    /**
     * @var int calculation precision (decimals count)
     */
    protected $precision;

    /**
     * @var string round mode : see self::ROUND_MODE_xxx
     */
    protected $roundType;

    /**
     * @var int|null
     */
    protected $orderId;

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
    protected $initialTotalPrice;

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
     * @param array $rowData array item given by Cart::getProducts()
     * @param PriceCalculator $priceCalculator
     * @param AddressFactory $addressFactory
     * @param CustomerDataProvider $customerDataProvider
     * @param CacheAdapter $cacheAdapter
     * @param GroupDataProvider $groupDataProvider
     * @param Database $databaseAdapter
     * @param bool $useEcotax
     * @param int $precision
     * @param string $roundType see self::ROUND_MODE_*
     * @param int|null $orderId If order ID is specified the product price is fetched from associated OrderDetail value
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
        $roundType,
        $orderId = null
    ) {
        $this->setRowData($rowData);
        $this->priceCalculator = $priceCalculator;
        $this->addressFactory = $addressFactory;
        $this->customerDataProvider = $customerDataProvider;
        $this->cacheAdapter = $cacheAdapter;
        $this->groupDataProvider = $groupDataProvider;
        $this->databaseAdapter = $databaseAdapter;
        $this->useEcotax = $useEcotax;
        $this->precision = $precision;
        $this->roundType = $roundType;
        $this->orderId = $orderId;
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
     * Returns the initial unit price (ie without applying cart rules).
     *
     * @return AmountImmutable
     *
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
     * Returns the initial total price (ie without applying cart rules).
     *
     * @return AmountImmutable
     *
     * @throws \Exception
     */
    public function getInitialTotalPrice()
    {
        if (!$this->isProcessed) {
            throw new \Exception('Row must be processed before getting its total');
        }

        return $this->initialTotalPrice;
    }

    /**
     * return final price: initial minus the cart rule discounts.
     *
     * @return AmountImmutable
     *
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
     * return final price: initial minus the cart rule discounts.
     *
     * @return AmountImmutable
     *
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
     * run initial row calculation.
     *
     * @param Cart $cart
     *
     * @throws CoreException
     */
    public function processCalculation(Cart $cart)
    {
        $rowData = $this->getRowData();
        $quantity = (int) $rowData['cart_quantity'];
        $this->initialUnitPrice = $this->getProductPrice($cart, $rowData);

        // store not rounded values, except in round_mode_item, we still need to round individual items
        if ($this->roundType == self::ROUND_MODE_ITEM) {
            $tools = new Tools();
            $this->initialTotalPrice = new AmountImmutable(
                $tools->round($this->initialUnitPrice->getTaxIncluded(), $this->precision) * $quantity,
                $tools->round($this->initialUnitPrice->getTaxExcluded(), $this->precision) * $quantity
            );
        } else {
            $this->initialTotalPrice = new AmountImmutable(
                $this->initialUnitPrice->getTaxIncluded() * $quantity,
                $this->initialUnitPrice->getTaxExcluded() * $quantity
            );
        }

        $this->finalTotalPrice = clone $this->initialTotalPrice;
        $this->applyRound();
        // store state
        $this->isProcessed = true;
    }

    protected function getProductPrice(Cart $cart, $rowData)
    {
        $productId = (int) $rowData['id_product'];
        $quantity = (int) $rowData['cart_quantity'];

        $addressId = $cart->getProductAddressId($rowData);
        if (!$addressId) {
            $addressId = $cart->getTaxAddressId();
        }
        $address = $this->addressFactory->findOrCreate($addressId, true);
        $countryId = (int) $address->id_country;
        $stateId = (int) $address->id_state;
        $zipCode = $address->postcode;

        $shopId = (int) $rowData['id_shop'];
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
            $cacheId = sprintf(self::PRODUCT_PRICE_CACHE_ID_PATTERN, (int) $productId, (int) $cart->id);
            if (!$this->cacheAdapter->isStored($cacheId)
                || ($cartQuantity = $this->cacheAdapter->retrieve($cacheId)
                                    != (int) $quantity)) {
                $sql = 'SELECT SUM(`quantity`)
				FROM `' . _DB_PREFIX_ . 'cart_product`
				WHERE `id_product` = ' . (int) $productId . '
				AND `id_cart` = ' . (int) $cart->id;
                $cartQuantity = (int) $this->databaseAdapter->getValue($sql, _PS_USE_SQL_SLAVE_);
                $this->cacheAdapter->store($cacheId, (string) $cartQuantity);
            } else {
                $cartQuantity = (int) $this->cacheAdapter->retrieve($cacheId);
            }
        }

        // The $null variable below is not used,
        // but it is necessary to pass it to getProductPrice because
        // it expects a reference.
        $specificPriceOutput = null;

        $productPrices = [
            'price_tax_included' => [
                'withTaxes' => true,
            ],
            'price_tax_excluded' => [
                'withTaxes' => false,
            ],
        ];
        foreach ($productPrices as $productPrice => $computationParameters) {
            $productPrices[$productPrice]['value'] = null;
            if (null !== $this->orderId) {
                $productPrices[$productPrice]['value'] = $this->priceCalculator->getOrderPrice(
                    $this->orderId,
                    (int) $productId,
                    (int) $rowData['id_product_attribute'],
                    $computationParameters['withTaxes'],
                    true,
                    $this->useEcotax
                );
            }
            if (null === $productPrices[$productPrice]['value']) {
                $productPrices[$productPrice]['value'] = $this->priceCalculator->priceCalculation(
                    $shopId,
                    (int) $productId,
                    (int) $rowData['id_product_attribute'],
                    $countryId,
                    $stateId,
                    $zipCode,
                    $currencyId,
                    $groupId,
                    $quantity,
                    $computationParameters['withTaxes'],
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
            }
        }

        return new AmountImmutable(
            $productPrices['price_tax_included']['value'],
            $productPrices['price_tax_excluded']['value']
        );
    }

    /**
     * depending on attribute roundType, rounds the item/line value.
     */
    protected function applyRound()
    {
        // ROUNDING MODE
        $this->finalUnitPrice = clone $this->initialUnitPrice;

        $rowData = $this->getRowData();
        $quantity = (int) $rowData['cart_quantity'];
        $tools = new Tools();
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
                $this->finalTotalPrice = new AmountImmutable(
                    $this->initialUnitPrice->getTaxIncluded() * $quantity,
                    $this->initialUnitPrice->getTaxExcluded() * $quantity
                );
                break;
        }
    }

    /**
     * substract discount from the row
     * if discount exceeds amount, we keep 0 (no use of negative amounts).
     *
     * @param AmountImmutable $amount
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
        $amount = new AmountImmutable($discountTaxIncluded, $discountTaxExcluded);
        $this->applyFlatDiscount($amount);

        return $amount;
    }

    /**
     * when final row price is calculated, we need to update unit price.
     */
    protected function updateFinalUnitPrice()
    {
        $rowData = $this->getRowData();
        $quantity = (int) $rowData['cart_quantity'];
        $taxIncluded = $this->finalTotalPrice->getTaxIncluded();
        $taxExcluded = $this->finalTotalPrice->getTaxExcluded();
        // Avoid division by zero
        if (0 === $quantity) {
            $this->finalUnitPrice = new AmountImmutable(0, 0);
        } else {
            $this->finalUnitPrice = new AmountImmutable(
                $taxIncluded / $quantity,
                $taxExcluded / $quantity
            );
        }
    }

    /**
     * @return string
     */
    public function getRoundType()
    {
        return $this->roundType;
    }
}
