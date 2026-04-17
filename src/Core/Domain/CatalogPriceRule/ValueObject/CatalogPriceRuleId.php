<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;

/**
 * Provides catalog price rule id
 */
final class CatalogPriceRuleId
{
    /**
     * @var int
     */
    private $catalogPriceRuleId;

    /**
     * @param int $catalogPriceRuleId
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function __construct(int $catalogPriceRuleId)
    {
        $this->assertIsGreaterThanZero($catalogPriceRuleId);
        $this->catalogPriceRuleId = $catalogPriceRuleId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * Validates that the value is greater than zero
     *
     * @param int $value
     *
     * @throws CatalogPriceRuleConstraintException
     */
    private function assertIsGreaterThanZero(int $value)
    {
        if (0 >= $value) {
            throw new CatalogPriceRuleConstraintException(sprintf('Invalid catalog price rule id "%s".', $value), CatalogPriceRuleConstraintException::INVALID_ID);
        }
    }
}
