<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

class ProductCombination
{
    /**
     * @var int
     */
    private $combinationId;

    /**
     * @var string
     */
    private $combinationName;

    /**
     * @param int $combinationId
     * @param string $combinationName
     */
    public function __construct(
        int $combinationId,
        string $combinationName
    ) {
        $this->combinationId = $combinationId;
        $this->combinationName = $combinationName;
    }

    /**
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    /**
     * @return string
     */
    public function getCombinationName(): string
    {
        return $this->combinationName;
    }
}
