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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDeleteProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Handles command which deletes products in bulk action
 */
final class BulkDeleteProductHandler extends AbstractBulkHandler implements BulkDeleteProductHandlerInterface
{
    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @param ProductMultiShopRepository $productRepository
     */
    public function __construct(ProductMultiShopRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteProductCommand $command): void
    {
        $this->handleBulkAction($command->getProductIds());
    }

    protected function handleSingleAction(ProductId $productId, $command = null)
    {
        //@todo: not sure about all shops constraint. Something might be confused here.
        // Im pretty sure it used to delete product only in context shop when we don't provide shops explicitly.
        // but in ProductController I see it used in bulkDeleteFromAllShopsAction.
        // So this whole command might deserve renaming, or its confused with DeleteProductFromShopsCommand (which seems to be unused at all)
        // need to double check this part before merging
        $this->productRepository->deleteByShopConstraint($productId, ShopConstraint::allShops());
    }

    protected function buildBulkException(): BulkProductException
    {
        return new CannotBulkDeleteProductException();
    }
}
