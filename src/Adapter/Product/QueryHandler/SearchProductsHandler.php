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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Address;
use Configuration;
use Currency;
use Order;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCombination;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCustomizationField;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use Product;
use Shop;

/**
 * Handles products search using legacy object model
 */
#[AsQueryHandler]
final class SearchProductsHandler extends AbstractOrderHandler implements SearchProductsHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var LocaleInterface
     */
    private $contextLocale;

    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var CurrencyDataProvider
     */
    private $currencyDataProvider;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param int $contextLangId
     * @param LocaleInterface $contextLocale
     * @param Tools $tools
     * @param CurrencyDataProvider $currencyDataProvider
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(
        int $contextLangId,
        LocaleInterface $contextLocale,
        Tools $tools,
        CurrencyDataProvider $currencyDataProvider,
        ContextStateManager $contextStateManager
    ) {
        $this->contextLangId = $contextLangId;
        $this->contextLocale = $contextLocale;
        $this->currencyDataProvider = $currencyDataProvider;
        $this->tools = $tools;
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     *
     * @param SearchProducts $query
     *
     * @return array
     */
    public function handle(SearchProducts $query): array
    {
        $currency = $this->currencyDataProvider->getCurrencyByIsoCode($query->getAlphaIsoCode()->getValue());
        if (null === $currency) {
            throw new CurrencyNotFoundException(sprintf('Could not find currency matching ISO code %s', $query->getAlphaIsoCode()->getValue()));
        }

        $this->contextStateManager
            ->setCurrency($currency)
        ;
        if (null !== $query->getOrderId()) {
            $order = $this->getOrder($query->getOrderId());
            $this->contextStateManager
                ->setShop(new Shop($order->id_shop))
            ;
        }

        try {
            $foundProducts = $this->searchProducts($query, $currency);
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }

        return $foundProducts;
    }

    /**
     * @param SearchProducts $query
     * @param Currency $currency
     *
     * @return array
     */
    private function searchProducts(SearchProducts $query, Currency $currency): array
    {
        $computingPrecision = new ComputingPrecision();
        $currencyPrecision = $computingPrecision->getPrecision((int) $currency->precision);

        $order = $address = null;
        if (null !== $query->getOrderId()) {
            $order = $this->getOrder($query->getOrderId());
            $orderAddressId = $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)};
            $address = new Address($orderAddressId);
        }

        $products = Product::searchByName(
            $this->contextLangId,
            $query->getPhrase(),
            null,
            $query->getResultsLimit()
        );

        $foundProducts = [];
        if ($products) {
            foreach ($products as $product) {
                $foundProduct = $this->createFoundProductFromLegacy(
                    new Product($product['id_product']),
                    $query->getAlphaIsoCode()->getValue(),
                    $currencyPrecision,
                    $order,
                    $address
                );
                $foundProducts[] = $foundProduct;
            }
        }

        return $foundProducts;
    }

    /**
     * @param Product $product
     * @param string $isoCodeCurrency
     * @param int $computingPrecision
     * @param Order|null $order
     * @param Address|null $address
     *
     * @return FoundProduct
     */
    private function createFoundProductFromLegacy(
        Product $product,
        string $isoCodeCurrency,
        int $computingPrecision,
        ?Order $order = null,
        ?Address $address = null
    ): FoundProduct {
        // It's important to use null (not 0) as attribute ID so that Product::priceCalculation can fallback to default combination
        $priceTaxExcluded = $this->getProductPriceForOrder((int) $product->id, null, false, $computingPrecision, $order) ?? 0.00;
        $priceTaxIncluded = $this->getProductPriceForOrder((int) $product->id, null, true, $computingPrecision, $order) ?? 0.00;
        $product->loadStockData();

        return new FoundProduct(
            $product->id,
            $product->name[$this->contextLangId],
            $this->contextLocale->formatPrice($priceTaxExcluded, $isoCodeCurrency),
            $this->tools->round($priceTaxIncluded, $computingPrecision),
            $this->tools->round($priceTaxExcluded, $computingPrecision),
            $product->getTaxesRate($address),
            Product::getQuantity($product->id),
            $product->location,
            (bool) Product::isAvailableWhenOutOfStock($product->out_of_stock),
            $this->getProductCombinations($product, $isoCodeCurrency, $computingPrecision, $order),
            $this->getProductCustomizationFields($product)
        );
    }

    /**
     * @param Product $product
     *
     * @return ProductCustomizationField[]
     */
    private function getProductCustomizationFields(Product $product): array
    {
        $fields = $product->getCustomizationFields();
        $customizationFields = [];

        if (false !== $fields) {
            foreach ($fields as $typeId => $typeFields) {
                foreach ($typeFields as $field) {
                    $customizationField = new ProductCustomizationField(
                        (int) $field[$this->contextLangId]['id_customization_field'],
                        (int) $typeId,
                        $field[$this->contextLangId]['name'],
                        (bool) $field[$this->contextLangId]['required']
                    );

                    $customizationFields[$customizationField->getCustomizationFieldId()] = $customizationField;
                }
            }
        }

        return $customizationFields;
    }

    /**
     * @param Product $product
     * @param string $currencyIsoCode
     * @param int $computingPrecision
     * @param Order|null $order
     *
     * @return array
     */
    private function getProductCombinations(
        Product $product,
        string $currencyIsoCode,
        int $computingPrecision,
        ?Order $order = null
    ): array {
        $productCombinations = [];
        $combinations = $product->getAttributeCombinations();

        if (false !== $combinations) {
            foreach ($combinations as $combination) {
                $productAttributeId = (int) $combination['id_product_attribute'];
                $attribute = $combination['attribute_name'];

                if (isset($productCombinations[$productAttributeId])) {
                    $existingAttribute = $productCombinations[$productAttributeId]->getAttribute();
                    $attribute = $existingAttribute . ' - ' . $attribute;
                }

                $priceTaxExcluded = $this->getProductPriceForOrder((int) $product->id, $productAttributeId, false, $computingPrecision, $order);
                $priceTaxIncluded = $this->getProductPriceForOrder((int) $product->id, $productAttributeId, true, $computingPrecision, $order);

                $productCombination = new ProductCombination(
                    $productAttributeId,
                    $attribute,
                    $combination['quantity'],
                    $this->contextLocale->formatPrice($priceTaxExcluded, $currencyIsoCode),
                    $priceTaxExcluded,
                    $priceTaxIncluded,
                    $combination['location'],
                    $combination['reference']
                );

                $productCombinations[$productCombination->getAttributeCombinationId()] = $productCombination;
            }
        }

        return $productCombinations;
    }

    /**
     * @param int $productId
     * @param int|null $productAttributeId
     * @param bool $withTaxes
     * @param int $computingPrecision
     * @param Order|null $order
     *
     * @return float|null
     */
    private function getProductPriceForOrder(
        int $productId,
        ?int $productAttributeId,
        bool $withTaxes,
        int $computingPrecision,
        ?Order $order)
    {
        if (null === $order) {
            return Product::getPriceStatic($productId, $withTaxes, $productAttributeId, $computingPrecision);
        }

        return Product::getPriceStatic(
            $productId,
            $withTaxes,
            $productAttributeId,
            $computingPrecision,
            null,
            false,
            true,
            1,
            false,
            $order->id_customer,
            $order->id_cart,
            $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
        );
    }
}
