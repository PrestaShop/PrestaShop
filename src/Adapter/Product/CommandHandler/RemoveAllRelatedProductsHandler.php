<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\RelatedProductsUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\RemoveAllRelatedProductsHandlerInterface;

/**
 * Handles @see RemoveAllRelatedProductsCommand using legacy object model
 */
#[AsCommandHandler]
final class RemoveAllRelatedProductsHandler implements RemoveAllRelatedProductsHandlerInterface
{
    /**
     * @var RelatedProductsUpdater
     */
    private $relatedProductsUpdater;

    /**
     * @param RelatedProductsUpdater $relatedProductsUpdater
     */
    public function __construct(
        RelatedProductsUpdater $relatedProductsUpdater
    ) {
        $this->relatedProductsUpdater = $relatedProductsUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RemoveAllRelatedProductsCommand $command): void
    {
        $this->relatedProductsUpdater->setRelatedProducts($command->getProductId(), []);
    }
}
