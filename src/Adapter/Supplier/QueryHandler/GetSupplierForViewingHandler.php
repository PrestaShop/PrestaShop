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

namespace PrestaShop\PrestaShop\Adapter\Supplier\QueryHandler;

use Context;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use PrestaShopException;
use Supplier;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\ViewableSupplier;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryHandler\GetSupplierForViewingHandlerInterface;
use Product;

/**
 * Handles query which gets supplier for viewing
 */
class GetSupplierForViewingHandler implements GetSupplierForViewingHandlerInterface
{
    /**
     * @var LocaleRepository
     */
    private $localeRepository;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param LocaleRepository $localeRepository
     * @param Context $context
     */
    public function __construct(LocaleRepository $localeRepository, Context $context)
    {
        $this->localeRepository = $localeRepository;
        $this->context = $context;
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
                sprintf('Supplier with id "%s" was not found.', $supplierId->getValue())
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
        $locale = $this->localeRepository->getLocale($this->context->language->getLocale());
        $currencyCode = $this->context->currency->iso_code;

        try {
            foreach ($supplierProducts as $productData) {
                $product = new Product($productData['id_product'], false, $languageId->getValue());
                $product->loadStockData();

                $productCombinations = $product->getAttributeCombinations($languageId->getValue());
                $combinations = [];

                foreach ($productCombinations as $combination) {
                    $attributeId = $combination['id_product_attribute'];
                    if (!isset($combinations[$attributeId])) {
                        $combinations[$attributeId] = [
                            'reference' => $combination['reference'],
                            'supplier_reference' => $combination['supplier_reference'],
                            'wholesale_price' => $locale->formatPrice($combination['wholesale_price'], $currencyCode),
                            'ean13' => $combination['ean13'],
                            'upc' => $combination['upc'],
                            'quantity' => $combination['quantity'],
                            'attributes' => '',
                        ];
                    }

                    $attribute = sprintf(
                        '%s - %s',
                        $combination['group_name'],
                        $combination['attribute_name']
                    );

                    if (!empty($combinations[$attributeId]['attributes'])) {
                        $attribute = sprintf(', %s', $attribute);
                    }

                    $combinations[$attributeId]['attributes'] .= $attribute;
                }
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'reference' => $product->reference,
                    'supplier_reference' => $product->supplier_reference,
                    'wholesale_price' => $locale->formatPrice($product->wholesale_price, $currencyCode),
                    'ean13' => $product->ean13,
                    'upc' => $product->upc,
                    'quantity' => $product->quantity,
                    'combinations' => $combinations,
                ];
            }
        } catch (PrestaShopException $e) {
            throw new SupplierException(
                sprintf('Failed to get products for supplier with id "%s".', $supplier->id)
            );
        }

        return $products;
    }
}
