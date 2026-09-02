<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Product;

/**
 * Fills product properties related to stock. But just the ones in Product entity and not the ones in StockAvailable.
 * For properties like quantity, out_of_stock and location @see UpdateProductStockAvailableCommand
 */
class StockInformationFiller implements ProductFillerInterface
{
    use LocalizedObjectModelTrait;

    /**
     * @param Product $product
     * @param UpdateProductCommand $command
     *
     * @return string[]|array<string, int[]>
     */
    public function fillUpdatableProperties(
        Product $product,
        UpdateProductCommand $command
    ): array {
        $updatableProperties = [];

        $localizedLaterLabels = $command->getLocalizedAvailableLaterLabels();
        if (null !== $localizedLaterLabels) {
            $this->fillLocalizedValues($product, 'available_later', $localizedLaterLabels, $updatableProperties);
        }

        $localizedNowLabels = $command->getLocalizedAvailableNowLabels();
        if (null !== $localizedNowLabels) {
            $this->fillLocalizedValues($product, 'available_now', $localizedNowLabels, $updatableProperties);
        }

        $lowStockThreshold = $command->getLowStockThreshold();
        if (null !== $lowStockThreshold) {
            $product->low_stock_threshold = $lowStockThreshold->getValue();
            $product->low_stock_alert = $lowStockThreshold->isEnabled();
            $updatableProperties[] = 'low_stock_threshold';
            $updatableProperties[] = 'low_stock_alert';
        }

        if (null !== $command->getMinimalQuantity()) {
            $product->minimal_quantity = $command->getMinimalQuantity();
            $updatableProperties[] = 'minimal_quantity';
        }
        if (null !== $command->getPackStockType()) {
            $product->pack_stock_type = $command->getPackStockType()->getValue();
            $updatableProperties[] = 'pack_stock_type';
        }
        if (null !== $command->getAvailableDate()) {
            $product->available_date = $command->getAvailableDate()->format(DateTime::DEFAULT_DATE_FORMAT);
            $updatableProperties[] = 'available_date';
        }

        return $updatableProperties;
    }
}
