<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\ValueObject;

/**
 * Ordered collection of PriceModification steps, representing the full audit trail of a price computation.
 */
class PriceBreakdown
{
    /** @var PriceModification[] */
    protected array $steps = [];

    public function addStep(PriceModification $step): void
    {
        $this->steps[] = $step;
    }

    /**
     * @return PriceModification[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    public function count(): int
    {
        return count($this->steps);
    }
}
