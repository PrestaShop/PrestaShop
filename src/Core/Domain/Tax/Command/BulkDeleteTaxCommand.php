<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Deletes taxes on bulk action
 */
class BulkDeleteTaxCommand
{
    /**
     * @var array<TaxId>
     */
    private $taxIds;

    /**
     * @param array<int> $taxIds
     *
     * @throws TaxException
     */
    public function __construct(array $taxIds)
    {
        $this->setTaxIds($taxIds);
    }

    /**
     * @return array<TaxId>
     */
    public function getTaxIds()
    {
        return $this->taxIds;
    }

    /**
     * @param array<int> $taxIds
     *
     * @throws TaxException
     */
    private function setTaxIds(array $taxIds)
    {
        foreach ($taxIds as $taxId) {
            $this->taxIds[] = new TaxId((int) $taxId);
        }
    }
}
