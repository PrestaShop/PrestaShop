<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\ToggleProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\ToggleProductStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotToggleProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopException;
use Product;

/**
 * Toggles product status on or off.
 *
 * @internal
 */
final class ToggleProductStatusHandler implements ToggleProductStatusHandlerInterface
{
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(HookDispatcherInterface $hookDispatcher)
    {
        $this->hookDispatcher = $hookDispatcher;
    }

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

        $status = (bool) !$entity->active;

        $this->dispatchBeforeHooks($status, $command->getProductId()->getValue());

        $entity->setFieldsToUpdate(['active' => true]);
        $entity->active = $status;

        try {
            if (false === $entity->update()) {
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

        $this->dispatchAfterHooks($status, $command->getProductId()->getValue());
    }

    /**
     * Dispatches before status change hooks.
     *
     * @param bool $status
     * @param int $productId
     */
    private function dispatchBeforeHooks($status, $productId)
    {
        $parameters = [
            'product_id' => $productId,
        ];

        $hookAdminBefore = $status ? 'actionAdminActivateBefore' : 'actionAdminDeactivateBefore';
        $hookProductController =
            $status ? 'actionAdminProductsControllerActivateBefore' : 'actionAdminProductsControllerDeactivateBefore';

        $this->hookDispatcher->dispatchWithParameters($hookAdminBefore, $parameters);
        $this->hookDispatcher->dispatchWithParameters($hookProductController, $parameters);
    }

    /**
     * Dispatches after status change hooks.
     *
     * @param bool $status
     * @param int $productId
     */
    private function dispatchAfterHooks($status, $productId)
    {
        $parameters = [
            'product_id' => $productId,
        ];

        $hookAdminAfter = $status ? 'actionAdminActivateAfter' : 'actionAdminDeactivateAfter';
        $hookProductControllerAfter =
            $status ? 'actionAdminProductsControllerActivateAfter' : 'actionAdminProductsControllerDeactivateAfter';

        $this->hookDispatcher->dispatchWithParameters($hookAdminAfter, $parameters);
        $this->hookDispatcher->dispatchWithParameters($hookProductControllerAfter, $parameters);
    }
}
