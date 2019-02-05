<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

class BulkDeleteTaxCommand
{
    /**
     * @var TaxId[]
     */
    private $taxesIds;

    /**
     * @param array $taxesIds
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException
     */
    public function __construct(array $taxesIds)
    {
        $this->setTaxesIds($taxesIds);
    }

    /**
     * @return TaxId[]
     */
    public function getTaxesIds()
    {
        return $this->taxesIds;
    }

    /**
     * @param int[] $taxesIds
     * @throws \PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException
     */
    private function setTaxesIds(array $taxesIds)
    {
        foreach ($taxesIds as $taxId) {
            $this->taxesIds[] = new TaxId($taxId);
        }
    }
}
