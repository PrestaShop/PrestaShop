<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetCombinationSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

/**
 * Retrieves data for product combination supplier
 *
 * @see GetCombinationSuppliersHandlerInterface
 */
class GetCombinationSuppliers
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @param int $combinationId
     */
    public function __construct(
        int $combinationId
    ) {
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
