<?php

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use Tax;

abstract class AbstractTaxHandler
{
    protected function assertTaxWasFound(TaxId $taxId, Tax $tax)
    {
        if ($tax->id !== $taxId->getValue()) {
            throw new TaxNotFoundException(sprintf(
                'Tax with id "%s" was not found.',
                $taxId->getValue())
            );
        }
    }
}
