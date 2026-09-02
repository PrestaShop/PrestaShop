<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

class ProductCombinationsCollection
{
    /**
     * @var ProductCombination[]
     */
    private $productCombinations;

    /**
     * @param ProductCombination[] $productCombinations
     */
    public function __construct(
        array $productCombinations
    ) {
        $this->productCombinations = $productCombinations;
    }

    /**
     * @return ProductCombination[]
     */
    public function getProductCombinations(): array
    {
        return $this->productCombinations;
    }
}
