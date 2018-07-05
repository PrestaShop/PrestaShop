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

namespace PrestaShop\PrestaShop\Adapter\NewProducts;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use Symfony\Component\Translation\TranslatorInterface;
use Product;

class NewProductsProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $sortOrderFactory;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        return Product::getNewProducts(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            'products' !== $type,
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        );
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        if (!$products = $this->getProductsOrCount($context, $query, 'products')) {
            $products = array();
        }
        $count = $this->getProductsOrCount($context, $query, 'count');

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
                array(
                    (new SortOrder('product', 'date_add', 'desc'))->setLabel(
                        $this->translator->trans('Date added, newest to oldest', array(), 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'date_add', 'asc'))->setLabel(
                        $this->translator->trans('Date added, oldest to newest', array(), 'Shop.Theme.Catalog')
                    ),
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
