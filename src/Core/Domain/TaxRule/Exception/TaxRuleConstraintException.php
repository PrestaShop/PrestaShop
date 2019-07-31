<?php

namespace PrestaShop\PrestaShop\Core\Domain\TaxRule\Exception;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;

class TaxRuleConstraintException extends TaxException
{
    public const INVALID_ID = 1;
}
