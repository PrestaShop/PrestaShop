<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkEnableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkEnableProductStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotEnableProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShopException;
use Product;

/**
 * bulk enables product status.
 *
 * @internal
 */
final class BulkEnableProductStatusHandler implements BulkEnableProductStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ProductNotFoundException
     * @throws CannotEnableProductException
     */
    public function handle(BulkEnableProductStatusCommand $command)
    {
        foreach ($command->getProductIds() as $productId) {
            $entity = new Product($productId->getValue());

            if (0 >= $entity->id) {
                throw new ProductNotFoundException(
                    sprintf('Product not found with given id %s', $productId->getValue())
                );
            }

            $entity->setFieldsToUpdate(['active' => true]);
            $entity->active = true;

            try {
                if (false === $entity->update()) {
                    throw new CannotEnableProductException(
                        sprintf(
                            'Failed to enable product with given id %s',
                            $productId->getValue()
                        )
                    );
                }
            } catch (PrestaShopException $exception) {
                throw new CannotEnableProductException(
                    sprintf(
                        'An unexpected error occurred when trying to enable product with id %s',
                        $productId->getValue()
                    ),
                    0,
                    $exception
                );
            }
        }
    }
}
