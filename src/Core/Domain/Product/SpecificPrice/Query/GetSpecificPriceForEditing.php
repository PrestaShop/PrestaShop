<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;

/**
 * Query which provides specific price for editing
 */
class GetSpecificPriceForEditing
{
    /**
     * @var SpecificPriceId
     */
    private $specificPriceId;

    /**
     * @param int $specificPriceId
     */
    public function __construct(
        int $specificPriceId
    ) {
        $this->specificPriceId = new SpecificPriceId($specificPriceId);
    }

    /**
     * @return SpecificPriceId
     */
    public function getSpecificPriceId(): SpecificPriceId
    {
        return $this->specificPriceId;
    }
}
