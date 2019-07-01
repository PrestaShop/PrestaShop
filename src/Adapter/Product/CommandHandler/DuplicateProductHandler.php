<?php

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\DuplicateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotDuplicateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
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
    public function handle(DuplicateProductCommand $command)
    {
        try {
            $productId = $this->productDataUpdater->duplicateProduct($command->getProductId()->getValue());
        } catch (UpdateProductException $exception) {
            throw new CannotDuplicateProductException(
                sprintf(
                    'Cannot duplicate product with id %s',
                    $command->getProductId()->getValue()
                ),
                0,
                $exception
            );
        }

        return new ProductId((int) $productId);
    }
}
