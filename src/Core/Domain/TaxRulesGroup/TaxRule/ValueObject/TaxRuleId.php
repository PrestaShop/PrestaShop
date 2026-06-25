<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\TaxRuleConstraintException;

/**
 * Provides tax rule identity
 */
class TaxRuleId
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @param int $id
     *
     * @throws TaxRuleConstraintException
     */
    public function __construct(int $id)
    {
        $this->assertPositiveInt($id);
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->id;
    }

    /**
     * @param int $value
     *
     * @throws TaxRuleConstraintException
     */
    private function assertPositiveInt(int $value): void
    {
        if (0 >= $value) {
            throw new TaxRuleConstraintException(
                sprintf('Invalid tax rule id "%s".', var_export($value, true)),
                TaxRuleConstraintException::INVALID_ID
            );
        }
    }
}
