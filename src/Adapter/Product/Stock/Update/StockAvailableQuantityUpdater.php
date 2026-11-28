<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Update;

use Db;
use DbQuery;
use StockAvailable;

/**
 * Single place writing an available quantity.
 *
 * Only the quantity is written, and a relative change is computed by the database,
 * so concurrent requests cannot overwrite each other.
 * It goes through StockAvailable::update(), so its hooks and its overrides apply as for any update.
 *
 * @internal use StockQuantityUpdater, this class is only open so tests can replace the database write
 */
class StockAvailableQuantityUpdater
{
    /**
     * On success the stock holds the quantity read back from the database.
     *
     * @param string[] $otherProperties properties already set on the stock, stored by the same query
     */
    public function applyDelta(StockAvailable $stockAvailable, int $deltaQuantity, array $otherProperties = []): bool
    {
        $stockAvailable->setQuantityDelta($deltaQuantity);
        try {
            return $this->write($stockAvailable, (int) $stockAvailable->quantity + $deltaQuantity, $otherProperties);
        } finally {
            $stockAvailable->setQuantityDelta(null);
        }
    }

    /**
     * @param string[] $otherProperties properties already set on the stock, stored by the same query
     */
    public function setQuantity(StockAvailable $stockAvailable, int $quantity, array $otherProperties = []): bool
    {
        return $this->write($stockAvailable, $quantity, $otherProperties);
    }

    /**
     * @param string[] $otherProperties
     */
    private function write(StockAvailable $stockAvailable, int $quantity, array $otherProperties): bool
    {
        $stockId = (int) $stockAvailable->id;
        if ($stockId <= 0) {
            return false;
        }

        // Listeners of the object hooks expect the quantity the stock is about to have
        $stockAvailable->quantity = $quantity;
        $stockAvailable->setFieldsToUpdate(array_fill_keys(['quantity', ...$otherProperties], true));
        try {
            $isUpdated = (bool) $stockAvailable->update();
        } finally {
            $stockAvailable->setFieldsToUpdate(null);
        }
        if (!$isUpdated) {
            return false;
        }

        $query = (new DbQuery())
            ->select('quantity')
            ->from('stock_available')
            ->where('id_stock_available = ' . $stockId)
        ;
        $stockAvailable->quantity = (int) Db::getInstance()->getValue($query, false);

        return true;
    }
}
