<?php

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxStatus;

/**
 * Class BulkUpdateTaxStatusCommand updates multiple Taxes status
 */
class BulkUpdateTaxStatusCommand
{
    /**
     * @var TaxStatus
     */
    private $status;

    /**
     * @var TaxId[]
     */
    private $taxesIds;

    /**
     * @param int[] $taxesIds
     * @param TaxStatus $status
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException
     */
    public function __construct(array $taxesIds, TaxStatus $status)
    {
        $this->status = $status;
        $this->setTaxesIds($taxesIds);
    }

    /**
     * @return TaxStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return TaxId[]
     */
    public function getTaxesIds()
    {
        return $this->taxesIds;
    }

    /**
     * @param array $taxesIds
     * @throws \PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException
     */
    private function setTaxesIds(array $taxesIds)
    {
        foreach ($taxesIds as $taxId) {
            $this->taxesIds[] = new TaxId($taxId);
        }
    }
}
