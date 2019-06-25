<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\ToggleProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\ToggleProductStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotToggleProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShopException;
use Product;

/**
 * Toggles product status on or off.
 */
final class ToggleProductStatusHandler implements ToggleProductStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotToggleProductException
     * @throws ProductNotFoundException
     */
    public function handle(ToggleProductStatusCommand $command)
    {
        $entity = new Product($command->getProductId()->getValue());

        if (0 >= $entity->id) {
            throw new ProductNotFoundException(
                sprintf('Product not found with given id %s', $command->getProductId()->getValue())
            );
        }

        try {
            if (false === $entity->toggleStatus()) {
                throw new CannotToggleProductException(
                    sprintf(
                        'Failed to toggle product with id %s',
                        $command->getProductId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $exception) {
            throw new CannotToggleProductException(
                sprintf(
                    'An unexpected error occurred when toggling product with id %s',
                    $command->getProductId()->getValue()
                ),
                0,
                $exception
            );
        }
    }
}
