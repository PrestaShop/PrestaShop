<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

/**
 * Transfers product combinations data
 */
class CombinationListForEditing
{
    /**
     * @var EditableCombinationForListing[]
     */
    private $combinations;

    /**
     * @var int
     */
    private $totalCombinationsCount;

    /**
     * @param int $totalCombinationsCount
     * @param EditableCombinationForListing[] $combinations
     */
    public function __construct(int $totalCombinationsCount, array $combinations)
    {
        $this->totalCombinationsCount = $totalCombinationsCount;
        $this->combinations = $combinations;
    }

    /**
     * @return EditableCombinationForListing[]
     */
    public function getCombinations(): array
    {
        return $this->combinations;
    }

    /**
     * @return int
     */
    public function getTotalCombinationsCount(): int
    {
        return $this->totalCombinationsCount;
    }
}
