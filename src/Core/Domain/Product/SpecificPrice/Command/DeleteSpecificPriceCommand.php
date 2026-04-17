<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;

/**
 * Delete specific price to a Product
 */
class DeleteSpecificPriceCommand
{
    /**
     * @var SpecificPriceId
     */
    protected $specificPriceId;

    /**
     * DeleteSpecificPriceCommand constructor.
     *
     * @param int $specificPriceId
     */
    public function __construct(int $specificPriceId)
    {
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
