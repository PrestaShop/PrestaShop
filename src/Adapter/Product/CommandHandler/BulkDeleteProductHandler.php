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

use InvalidArgumentException;
use PrestaShop\PrestaShop\Adapter\Product\ProductDeleter;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDeleteProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles command which deletes products in bulk action
 */
final class BulkDeleteProductHandler extends AbstractBulkHandler implements BulkDeleteProductHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductDeleter
     */
    private $productDeleter;

    public function __construct(
        ProductRepository $productRepository,
        ProductDeleter $productDeleter
    ) {
        $this->productRepository = $productRepository;
        $this->productDeleter = $productDeleter;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteProductCommand $command): void
    {
        $this->handleBulkAction($command->getProductIds(), $command);
    }

    /**
     * @param ProductId $productId
     * @param BulkDeleteProductCommand|null $command
     *
     * @return void
     */
    protected function handleSingleAction(ProductId $productId, $command = null): void
    {
        if (!($command instanceof BulkDeleteProductCommand)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected argument $command of type "%s". Got "%s"',
                    BulkDeleteProductCommand::class,
                    var_export($command, true)
                ));
        }

        $this->productDeleter->deleteFromShops(
            $productId,
            $this->productRepository->getShopIdsByConstraint($productId, $command->getShopConstraint())
        );
    }

    protected function buildBulkException(): BulkProductException
    {
        return new CannotBulkDeleteProductException();
    }
}
