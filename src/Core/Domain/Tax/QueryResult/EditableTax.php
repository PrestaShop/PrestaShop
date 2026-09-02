<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Transfers editable tax data
 */
class EditableTax
{
    /**
     * @var TaxId
     */
    private $taxId;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var float
     */
    private $rate;

    /**
     * @var bool
     */
    private $active;

    /**
     * EditableTax constructor.
     *
     * @param TaxId $taxId
     * @param string[] $localizedNames
     * @param float $rate
     * @param bool $active
     */
    public function __construct(TaxId $taxId, array $localizedNames, $rate, $active)
    {
        $this->taxId = $taxId;
        $this->localizedNames = $localizedNames;
        $this->rate = $rate;
        $this->active = $active;
    }

    /**
     * @return TaxId
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }
}
