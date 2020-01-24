<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\DuplicateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Exception\UpdateProductException;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface;

/**
 * Duplicates product.
 *
 * @internal
 */
final class DuplicateProductHandler implements DuplicateProductHandlerInterface
{
    /**
     * @var ProductInterface
     */
    private $productDataUpdater;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @param ProductInterface $productDataUpdater
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        ProductInterface $productDataUpdater,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->productDataUpdater = $productDataUpdater;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotDuplicateProductException
     */
    public function handle(DuplicateProductCommand $command)
    {
        $hookParameters = ['product_id' => $command->getProductId()->getValue()];

        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminDuplicateBefore',
            $hookParameters
        );

        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminProductsControllerDuplicateBefore',
            $hookParameters
        );

        try {
            $productId = $this->productDataUpdater->duplicateProduct($command->getProductId()->getValue());
        } catch (UpdateProductException $exception) {
            throw new CannotDuplicateProductException(sprintf('Cannot duplicate product with id %s', $command->getProductId()->getValue()), 0, $exception);
        }

        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminDuplicateAfter',
            $hookParameters
        );

        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminProductsControllerDuplicateAfter',
            $hookParameters
        );

        return new ProductId((int) $productId);
    }
}
