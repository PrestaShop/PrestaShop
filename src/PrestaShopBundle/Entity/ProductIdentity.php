<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductIdentity
{
    /**
     * @var int
     */
    private int $productId;

    /**
     * @var int
     */
    private int $combinationId = 0;

    public function __construct(int $productId, int $combinationId = 0)
    {
        $this->productId = $productId;
        $this->combinationId = $combinationId;
    }

    public static function fromArray(array $identifiers): ProductIdentity
    {
        if (!array_key_exists('product_id', $identifiers)) {
            throw new BadRequestHttpException('The "productId" parameter is required');
        }

        $productId = (int) $identifiers['product_id'];

        $combinationId = 0;
        if (array_key_exists('combination_id', $identifiers)) {
            $combinationId = (int) $identifiers['combination_id'];
        }

        return new static($productId, $combinationId);
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getCombinationId(): int
    {
        return $this->combinationId;
    }
}
