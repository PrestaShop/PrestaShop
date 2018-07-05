<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\BestSales;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use Symfony\Component\Translation\TranslatorInterface;
use ProductSale;
use Tools;

class BestSalesProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $sortOrderFactory;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $sortBySales = (new SortOrder('product', 'sales', 'desc'))->setLabel(
            $this->translator->trans('Sales, highest to lowest', array(), 'Shop.Theme.Catalog')
        );

        if (!Tools::getValue('order', 0)) {
            $query->setSortOrder($sortBySales);
        }

        if (!$products = ProductSale::getBestSales(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        )) {
            $products = array();
        }

        $count = (int) ProductSale::getNbSales();

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
                array(
                    $sortBySales,
                    (new SortOrder('product', 'name', 'asc'))->setLabel(
                        $this->translator->trans('Name, A to Z', array(), 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'name', 'desc'))->setLabel(
                        $this->translator->trans('Name, Z to A', array(), 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'price', 'asc'))->setLabel(
                        $this->translator->trans('Price, low to high', array(), 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'price', 'desc'))->setLabel(
                        $this->translator->trans('Price, high to low', array(), 'Shop.Theme.Catalog')
                    ),
                )
            );
        }

        return $result;
    }
}
