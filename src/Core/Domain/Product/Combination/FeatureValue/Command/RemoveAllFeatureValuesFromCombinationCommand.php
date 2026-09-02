<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

/**
 * Removes all combination feature values
 */
class RemoveAllFeatureValuesFromCombinationCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    public function __construct(int $combinationId)
    {
        $this->combinationId = new CombinationId($combinationId);
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }
}
