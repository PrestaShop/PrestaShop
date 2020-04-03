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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductBaseData;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetProductBaseDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductBaseData;
use Product;

final class GetProductBaseDataHandler implements GetProductBaseDataHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(GetProductBaseData $query): ProductBaseData
    {
        $productId = $query->getProductId()->getValue();
        // should we load whole product or use specific sql queries somewhere?
        //@todo: move to abstract handler. try catch. validate its id after loading
        $product = new Product($productId);

        return new ProductBaseData(
            $product->name,
            $product->getType()
        );
    }
}
