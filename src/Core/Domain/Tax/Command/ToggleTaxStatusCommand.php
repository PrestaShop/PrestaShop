<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Class ToggleTaxStatusCommand is responsible for changing Tax status
 */
class ToggleTaxStatusCommand
{
    /**
     * @var TaxId
     */
    private $taxId;

    /**
     * @param int $taxId
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException
     */
    public function __construct($taxId)
    {
        $this->taxId = new TaxId($taxId);
    }

    /**
     * @return TaxId
     */
    public function getTaxId()
    {
        return $this->taxId;
    }
}
