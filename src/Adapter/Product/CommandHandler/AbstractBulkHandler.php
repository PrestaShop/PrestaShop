<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * This abstract class helps us build a bulk handler since the principle is often very similar, it might not be
 * compatible with all the handlers, but it helps for many.
 */
abstract class AbstractBulkHandler
{
    /**
     * @param ProductId[] $productIds
     * @param mixed|null $command
     *
     * @return array<int, mixed>
     *
     * @throws BulkProductException
     */
    protected function handleBulkAction(array $productIds, $command = null): array
    {
        $bulkException = null;
        $actionResults = [];
        foreach ($productIds as $productId) {
            try {
                $actionResults[$productId->getValue()] = $this->handleSingleAction($productId, $command);
            } catch (ProductException $e) {
                if (null === $bulkException) {
                    $bulkException = $this->buildBulkException();
                }
                $bulkException->addException($productId, $e);
            }
        }

        if (null !== $bulkException) {
            throw $bulkException;
        }

        return $actionResults;
    }

    /**
     * This uses the base bulk exception class, but you can override this in your handler.
     *
     * @return BulkProductException
     */
    protected function buildBulkException(): BulkProductException
    {
        return new BulkProductException();
    }

    /**
     * @param ProductId $productId
     * @param mixed|null $command
     *
     * @return mixed
     */
    abstract protected function handleSingleAction(ProductId $productId, $command = null);
}
