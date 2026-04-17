<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Query;

use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Gets tax for editing in Back Office
 */
class GetTaxForEditing
{
    /**
     * @var TaxId
     */
    private $taxId;

    /**
     * @param int $taxId
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
