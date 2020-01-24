<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDisableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDisableProductStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDisableProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopException;
use Product;

/**
 * bulk disable product status.
 *
 * @internal
 */
final class BulkDisableProductStatusHandler implements BulkDisableProductStatusHandlerInterface
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
     * @throws ProductNotFoundException
     * @throws CannotDisableProductException
     */
    public function handle(BulkDisableProductStatusCommand $command)
    {
        $ids = array_map(static function (ProductId $item) { return $item->getValue(); }, $command->getProductIds());

        $hookParameters = ['product_list_id' => $ids];

        $this->hookDispatcher->dispatchWithParameters('actionAdminDeactivateBefore', $hookParameters);
        $this->hookDispatcher->dispatchWithParameters('actionAdminProductsControllerDeactivateBefore', $hookParameters);

        foreach ($command->getProductIds() as $productId) {
            $entity = new Product($productId->getValue());

            if (0 >= $entity->id) {
                throw new ProductNotFoundException(sprintf('Product not found with given id %s', $productId->getValue()));
            }

            $entity->setFieldsToUpdate(['active' => true]);
            $entity->active = false;

            try {
                if (false === $entity->update()) {
                    throw new CannotDisableProductException(sprintf('Failed to disable product with given id %s', $productId->getValue()));
                }
            } catch (PrestaShopException $exception) {
                throw new CannotDisableProductException(sprintf('An unexpected error occurred when trying to disable product with id %s', $productId->getValue()), 0, $exception);
            }
        }

        $this->hookDispatcher->dispatchWithParameters('actionAdminDeactivateAfter', $hookParameters);
        $this->hookDispatcher->dispatchWithParameters('actionAdminProductsControllerDeactivateAfter', $hookParameters);
    }
}
