<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Adapter\Search;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Search;
use Tools;

class SearchProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $category;
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
        $products = [];
        $count    = 0;

        if (($string = $query->getSearchString())) {
            $result = Search::find(
                $context->getIdLang(),
                Tools::replaceAccentedChars(urldecode($string)),
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(),
                $query->getSortOrder()->toLegacyOrderWay(),
                false, // ajax, what's the link?
                false, // $use_cookie, ignored anyway
                null
            );
            $products = $result['result'];
            $count    = $result['total'];
        } elseif (($tag = $query->getSearchTag())) {
            $products = Search::searchTag(
                $context->getIdLang(),
                urldecode($tag),
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
                urldecode($tag),
                true,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(true),
                $query->getSortOrder()->toLegacyOrderWay(),
                false,
                null
            );
        }

        $result = new ProductSearchResult();
        $result
            ->setProducts($products)
            ->setTotalProductsCount($count)
        ;

        $result->setAvailableSortOrders(
            $this->sortOrderFactory->getDefaultSortOrders()
        );

        return $result;
    }
}
