<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Exception;

/**
 * Class TaxConstraintException is thrown when Tax is invalid
 */
class TaxConstraintException extends TaxException
{
    const INVALID_TAX_ID = 10;
    const UNDEFINED_TAX_STATUS = 20;
}
