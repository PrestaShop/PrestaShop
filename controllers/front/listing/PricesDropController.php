<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\PricesDrop\PricesDropProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class PricesDropControllerCore extends ProductListingFrontController
{
    public $php_self = 'prices-drop';

    /**
     * {@inheritdoc}
     */
    public function initContent()
    {
        parent::initContent();

        $this->doProductSearch('catalog/listing/prices-drop');
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('prices-drop')
            ->setSortOrder(new SortOrder('product', 'name', 'asc'));

        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new PricesDropProductSearchProvider(
            $this->getTranslator()
        );
    }

    public function getListingLabel()
    {
        return $this->trans(
            'On sale',
            [],
            'Shop.Theme.Catalog'
        );
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('On sale', [], 'Shop.Theme.Catalog'),
            'url' => $this->context->link->getPageLink('prices-drop', true),
        ];

        return $breadcrumb;
    }
}
