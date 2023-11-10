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

namespace PrestaShop\PrestaShop\Adapter\Search;

use Hook;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use Search;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

/**
 * Class responsible of retrieving products in Search page of Front Office.
 *
 * @see SearchController
 */
class SearchProductSearchProvider implements ProductSearchProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SortOrderFactory
     */
    private $sortOrderFactory;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    /**
     * {@inheritdoc}
     */
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $products = [];
        $count = 0;

        if (($string = $query->getSearchString())) {
            $queryString = Tools::replaceAccentedChars(urldecode($string));

            $result = Search::find(
                $context->getIdLang(),
                $queryString,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(),
                $query->getSortOrder()->toLegacyOrderWay(),
                false, // ajax, what's the link?
                false, // $use_cookie, ignored anyway
                null
            );
            $products = $result['result'];
            $count = $result['total'];

            Hook::exec('actionSearch', [
                'searched_query' => $queryString,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $queryString,
            ]);
        } elseif (($tag = $query->getSearchTag())) {
            $queryString = urldecode($tag);

            $products = Search::searchTag(
                $context->getIdLang(),
                $queryString,
                false,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(true),
                $query->getSortOrder()->toLegacyOrderWay(),
                false,
                null
            );

            $count = Search::searchTag(
                $context->getIdLang(),
                $queryString,
                true,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(true),
                $query->getSortOrder()->toLegacyOrderWay(),
                false,
                null
            );

            Hook::exec('actionSearch', [
                'searched_query' => $queryString,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $queryString,
            ]);
        }

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
				[
					(new SortOrder('product', 'position', 'desc'))->setLabel(
						$this->translator->trans('Relevance', [], 'Shop.Theme.Catalog')
					),
					(new SortOrder('product', 'name', 'asc'))->setLabel(
						$this->translator->trans('Name, A to Z', [], 'Shop.Theme.Catalog')
					),
					(new SortOrder('product', 'name', 'desc'))->setLabel(
						$this->translator->trans('Name, Z to A', [], 'Shop.Theme.Catalog')
					),
					(new SortOrder('product', 'price', 'asc'))->setLabel(
						$this->translator->trans('Price, low to high', [], 'Shop.Theme.Catalog')
					),
					(new SortOrder('product', 'price', 'desc'))->setLabel(
						$this->translator->trans('Price, high to low', [], 'Shop.Theme.Catalog')
					),
				]	
            );
        }

        return $result;
    }
}
