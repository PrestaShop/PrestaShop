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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductDuplicator;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDuplicateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles command which deletes addresses in bulk action
 */
class BulkDuplicateProductHandler extends AbstractBulkHandler implements BulkDuplicateProductHandlerInterface
{
    /**
     * @var ProductDuplicator
     */
    private $productDuplicator;

    /**
     * @param ProductDuplicator $productDuplicator
     */
    public function __construct(ProductDuplicator $productDuplicator)
    {
        $this->productDuplicator = $productDuplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDuplicateProductCommand $command): array
    {
        return $this->handleBulkAction($command->getProductIds(), $command);
    }

    /**
     * @param ProductId $productId
     * @param BulkDuplicateProductCommand $command
     *
     * @return ProductId
     */
    protected function handleSingleAction(ProductId $productId, $command = null)
    {
        return $this->productDuplicator->duplicate($productId, $command->getShopConstraint());
    }

    protected function buildBulkException(): BulkProductException
    {
        return new CannotBulkDuplicateProductException();
    }
}
