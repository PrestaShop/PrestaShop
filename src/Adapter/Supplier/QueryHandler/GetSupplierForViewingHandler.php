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

namespace PrestaShop\PrestaShop\Adapter\Supplier\QueryHandler;

use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryHandler\GetSupplierForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\ViewableSupplier;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use Product;
use Supplier;

/**
 * Handles query which gets supplier for viewing
 */
#[AsQueryHandler]
final class GetSupplierForViewingHandler implements GetSupplierForViewingHandlerInterface
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @param Locale $locale
     * @param int $defaultCurrencyId
     */
    public function __construct(
        Locale $locale,
        int $defaultCurrencyId
    ) {
        $this->locale = $locale;
        $this->defaultCurrencyId = $defaultCurrencyId;
    }

    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     * @throws LocalizationException
     */
    public function handle(GetSupplierForViewing $query)
    {
        $supplier = $this->getSupplier($query->getSupplierId());

        return new ViewableSupplier(
            $supplier->name,
            $this->getSupplierProducts($supplier, $query->getLanguageId())
        );
    }

    /**
     * @param SupplierId $supplierId
     *
     * @return Supplier
     *
     * @throws SupplierNotFoundException
     */
    private function getSupplier(SupplierId $supplierId)
    {
        $supplier = new Supplier($supplierId->getValue());

        if ($supplier->id !== $supplierId->getValue()) {
            throw new SupplierNotFoundException(
                sprintf('Supplier with id "%d" was not found.', $supplierId->getValue())
            );
        }

        return $supplier;
    }

    /**
     * @param Supplier $supplier
     * @param LanguageId $languageId
     *
     * @return array
     *
     * @throws LocalizationException
     * @throws SupplierException
     */
    private function getSupplierProducts(Supplier $supplier, LanguageId $languageId)
    {
        $products = [];
        $supplierProducts = $supplier->getProductsLite($languageId->getValue());

        foreach ($supplierProducts as $productData) {
            $product = new Product($productData['id_product'], false, $languageId->getValue());
            $product->loadStockData();
            $combinations = $this->findProductSupplierCombinations($product, $supplier, $languageId);

            if (empty($combinations)) {
                $products[] = $this->buildNonCombinationSupplierProduct($product, $supplier);
                continue;
            }

            $products[] = [
                'id' => $product->id,
                'name' => $product->name,
                'combinations' => $combinations,
            ];
        }

        return $products;
    }

    /**
     * @param Product $product
     * @param Supplier $supplier
     *
     * @return array<string, mixed>
     *
     * @throws LocalizationException
     */
    private function buildNonCombinationSupplierProduct(Product $product, Supplier $supplier): array
    {
        $productInfo = Supplier::getProductInformationsBySupplier($supplier->id, $product->id);
        $product->wholesale_price = $productInfo['product_supplier_price_te'];
        $product->supplier_reference = $productInfo['product_supplier_reference'];
        $isoCode = Currency::getIsoCodeById((int) $productInfo['id_currency']) ?: Currency::getIsoCodeById($this->defaultCurrencyId);
        $formattedWholesalePrice = null !== $product->wholesale_price
            ? $this->locale->formatPrice($product->wholesale_price, $isoCode)
            : null;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'reference' => $product->reference,
            'supplier_reference' => $product->supplier_reference,
            'wholesale_price' => $formattedWholesalePrice,
            'ean13' => $product->ean13,
            'upc' => $product->upc,
            'quantity' => $product->quantity,
            'combinations' => [],
        ];
    }

    /**
     * @param Product $product
     * @param Supplier $supplier
     * @param LanguageId $languageId
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws LocalizationException
     */
    private function findProductSupplierCombinations(Product $product, Supplier $supplier, LanguageId $languageId): array
    {
        $productCombinations = $product->getAttributeCombinations($languageId->getValue());
        if (empty($productCombinations)) {
            return [];
        }

        $combinations = [];
        foreach ($productCombinations as $combination) {
            $attributeId = $combination['id_product_attribute'];
            if (!isset($combinations[$attributeId])) {
                $combinationSupplierInfo = Supplier::getProductInformationsBySupplier(
                    $supplier->id,
                    $product->id,
                    $combination['id_product_attribute']
                );
                if (!$combinationSupplierInfo) {
                    continue;
                }
                $isoCode = Currency::getIsoCodeById((int) $combinationSupplierInfo['id_currency'])
                    ?: Currency::getIsoCodeById($this->defaultCurrencyId);
                $formattedWholesalePrice = null !== $combinationSupplierInfo['product_supplier_price_te']
                    ? $this->locale->formatPrice($combinationSupplierInfo['product_supplier_price_te'], $isoCode)
                    : null;
                $combinations[$attributeId] = [
                    'reference' => $combination['reference'],
                    'supplier_reference' => $combinationSupplierInfo['product_supplier_reference'],
                    'wholesale_price' => $formattedWholesalePrice,
                    'ean13' => $combination['ean13'],
                    'upc' => $combination['upc'],
                    'quantity' => $combination['quantity'],
                    'attributes' => $this->buildCombinationName($combination),
                ];
                continue;
            }

            // if combination info already filled, we only append attributes to combination name
            $combinations[$attributeId]['attributes'] .= sprintf(', %s', $this->buildCombinationName($combination));
        }

        return $combinations;
    }

    /**
     * @param array<string, mixed> $attributesInfo
     *
     * @return string
     */
    private function buildCombinationName(array $attributesInfo): string
    {
        return sprintf('%s - %s', $attributesInfo['group_name'], $attributesInfo['attribute_name']);
    }
}
