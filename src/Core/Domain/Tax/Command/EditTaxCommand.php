<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Edits given tax with provided data
 */
class EditTaxCommand
{
    /**
     * @var TaxId
     */
    private $taxId;

    /**
     * @var array|null
     */
    private $localizedNames;

    /**
     * @var float|null
     */
    private $rate;

    /**
     * @var bool|null
     */
    private $enabled;

    /**
     * @param int $taxId
     *
     * @throws TaxException
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

    /**
     * @return array|null
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param array|null $localizedNames
     *
     * @return self
     */
    public function setLocalizedNames($localizedNames)
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float|null $rate
     *
     * @return self
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool|null $enabled
     *
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}
