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

use PrestaShop\PrestaShop\Adapter\OnsaleProducts\OnsaleProductsProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class OnsaleProductsControllerCore extends ProductListingFrontController
{
    public $php_self = 'onsale-products';

    /**
     * {@inheritdoc}
     */
    public function initContent()
    {
        parent::initContent();

        $this->doProductSearch('catalog/listing/onsale-products', ['entity' => 'onsale-products']);
    }

    protected function getProductSearchQuery(): ProductSearchQuery
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('onsale-products')
            ->setSortOrder(new SortOrder('product', 'name', 'asc'));

        return $query;
    }

    protected function getDefaultProductSearchProvider(): OnsaleProductsProductSearchProvider
    {
        return new OnsaleProductsProductSearchProvider(
            $this->getTranslator()
        );
    }

    public function getListingLabel(): string
    {
        return $this->trans(
            'Products on sale',
            [],
            'Shop.Theme.Catalog'
        );
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Products on sale', [], 'Shop.Theme.Catalog'),
            'url' => $this->context->link->getPageLink('onsale-products', true),
        ];

        return $breadcrumb;
    }
}
