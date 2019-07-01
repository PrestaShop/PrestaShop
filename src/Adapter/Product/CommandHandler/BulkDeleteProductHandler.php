<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDeleteProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDeleteProductException;
use PrestaShopException;
use Product;

/**
 * Deletes multiple products.
 *
 * @internal
 */
final class BulkDeleteProductHandler implements BulkDeleteProductHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteProductException
     */
    public function handle(BulkDeleteProductCommand $command)
    {
        $productIds = [];
        foreach ($command->getProductIds() as $productId) {
            $productIds[] = $productId->getValue();
        }

        try {
            $result = (new Product())->deleteSelection($productIds);

            if (!$result) {
                throw new CannotDeleteProductException(
                    sprintf(
                        'Failed to delete products with ids "%s"',
                        var_export($productIds, true)
                    )
                );
            }
        } catch (PrestaShopException $exception) {
            throw new CannotDeleteProductException(
                sprintf(
                    'An unexpected error occurred when trying to delete products with ids "%s"',
                    var_export($productIds, true)
                ),
                0,
                $exception
            );
        }
    }
}
