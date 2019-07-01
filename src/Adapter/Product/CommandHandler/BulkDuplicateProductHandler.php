<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDuplicateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShopBundle\Exception\UpdateProductException;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface;

final class BulkDuplicateProductHandler implements BulkDuplicateProductHandlerInterface
{
    /**
     * @var ProductInterface
     */
    private $productDataUpdater;

    /**
     * @param ProductInterface $productDataUpdater
     */
    public function __construct(ProductInterface $productDataUpdater)
    {
        $this->productDataUpdater = $productDataUpdater;
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

        try {
            $this->productDataUpdater->duplicateProductIdList($ids);
        } catch (UpdateProductException $exception) {
            throw new CannotDuplicateProductException(
                'Cannot duplicate products',
                0,
                $exception
            );
        }
    }
}
