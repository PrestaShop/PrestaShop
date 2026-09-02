<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Command for setting single tax rules group status
 */
class SetTaxRulesGroupStatusCommand
{
    /**
     * @var TaxRulesGroupId
     */
    private $taxRulesGroupId;

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param int $taxRulesGroupId
     * @param bool $expectedStatus
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function __construct(int $taxRulesGroupId, bool $expectedStatus)
    {
        $this->expectedStatus = $expectedStatus;
        $this->taxRulesGroupId = new TaxRulesGroupId($taxRulesGroupId);
    }

    /**
     * @return TaxRulesGroupId
     */
    public function getTaxRulesGroupId(): TaxRulesGroupId
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @return bool
     */
    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }
}
