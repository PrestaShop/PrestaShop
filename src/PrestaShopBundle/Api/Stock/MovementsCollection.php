<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Api\Stock;

use PrestaShopBundle\Entity\ProductIdentity;

class MovementsCollection
{
    private $movements = [];

    /**
     * @param array $stockMovementsParams
     *
     * @return $this
     */
    public function fromArray(array $stockMovementsParams)
    {
        $movements = [];

        array_walk($stockMovementsParams, function ($item) use (&$movements) {
            $combinationId = 0;

            if ($item['delta'] != 0) {
                if (array_key_exists('combination_id', $item)) {
                    $combinationId = $item['combination_id'];
                }

                $productIdentity = ProductIdentity::fromArray([
                    'product_id' => $item['product_id'],
                    'combination_id' => $combinationId,
                ]);

                $movements[] = new Movement($productIdentity, $item['delta']);
            }
        });

        $this->movements = $movements;

        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function map(callable $callback)
    {
        return array_map($callback, $this->movements);
    }
}
