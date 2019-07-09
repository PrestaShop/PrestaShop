<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDuplicateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Exception\UpdateProductException;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface;

final class BulkDuplicateProductHandler implements BulkDuplicateProductHandlerInterface
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
    public function handle(BulkDuplicateProductCommand $command)
    {
        $ids = [];
        foreach ($command->getProductIds() as $productId) {
            $ids[] = $productId->getValue();
        }

        $hookParameters = ['product_list_id' => $ids];

        $this->hookDispatcher->dispatchWithParameters('actionAdminDuplicateBefore', $hookParameters);
        $this->hookDispatcher->dispatchWithParameters('actionAdminProductsControllerDuplicateBefore', $hookParameters);

        try {
            $this->productDataUpdater->duplicateProductIdList($ids);
        } catch (UpdateProductException $exception) {
            throw new CannotDuplicateProductException(
                'Cannot duplicate products',
                0,
                $exception
            );
        }

        $this->hookDispatcher->dispatchWithParameters('actionAdminDuplicateAfter', $hookParameters);
        $this->hookDispatcher->dispatchWithParameters('actionAdminProductsControllerDuplicateAfter', $hookParameters);
    }
}
