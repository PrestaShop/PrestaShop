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

namespace PrestaShop\PrestaShop\Core\Product;

use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface as ProductDataProviderInterface;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * Used to export list of Products in CSV in the Product list page.
 * For internal use only.
 */
final class ProductCsvExporter implements ProductExporterInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ProductDataProviderInterface
     */
    private $productProvider;

    public function __construct(TranslatorInterface $translator, ProductDataProviderInterface $productProvider)
    {
        $this->translator = $translator;
        $this->productProvider = $productProvider;
    }

    /**
     * In this specific case, we don't need to pass a products list.
     *
     * @param array $products
     *
     * @return CsvResponse
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    public function export(array $products = [])
    {
        $productProvider = $this->productProvider;
        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        $orderBy = $persistedFilterParameters['last_orderBy'];
        $sortOrder = $persistedFilterParameters['last_sortOrder'];

        // prepare callback to fetch data from DB
        $dataCallback = function ($offset, $limit) use ($productProvider, $orderBy, $sortOrder) {
            return $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder, [], true, false);
        };

        $headersData = [
            'id_product' => 'Product ID',
            'image_link' => $this->trans('Image', 'Admin.Global'),
            'name' => $this->trans('Name', 'Admin.Global'),
            'reference' => $this->trans('Reference', 'Admin.Global'),
            'name_category' => $this->trans('Category', 'Admin.Global'),
            'price' => $this->trans('Price (tax excl.)', 'Admin.Catalog.Feature'),
            'price_final' => $this->trans('Price (tax incl.)', 'Admin.Catalog.Feature'),
            'sav_quantity' => $this->trans('Quantity', 'Admin.Global'),
            'badge_danger' => $this->trans('Status', 'Admin.Global'),
            'position' => $this->trans('Position', 'Admin.Global'),
        ];

        return (new CsvResponse())
            ->setData($dataCallback)
            ->setHeadersData($headersData)
            ->setModeType(CsvResponse::MODE_OFFSET)
            ->setLimit(5000)
            ->setFileName('product_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * Translator helper.
     *
     * @param string $key
     * @param string $domain
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    private function trans(string $key, string $domain): string
    {
        return $this->translator->trans($key, [], $domain);
    }
}
