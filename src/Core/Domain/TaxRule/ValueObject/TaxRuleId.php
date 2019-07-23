<?php

namespace PrestaShop\PrestaShop\Core\Domain\TaxRule\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\TaxRule\Exception\TaxRuleConstraintException;

/**
 * Holds unique identifier of tax rule.
 */
class TaxRuleId
{
    /**
     * @param int $taxRuleId
     *
     * @throws TaxRuleConstraintException
     */
    public function __construct(int $taxRuleId)
    {
        if (0 > $taxRuleId) {
            throw new TaxRuleConstraintException(
                'Tax rule id must not be negative number',
                TaxRuleConstraintException::INVALID_ID
            );
        }
    }
}
