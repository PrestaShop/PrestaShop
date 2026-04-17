<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\Command;

/**
 * Adds new tax
 */
class AddTaxCommand
{
    /**
     * @var array
     */
    private $localizedNames;

    /**
     * @var float
     */
    private $rate;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param array $localizedNames
     * @param float $rate
     * @param bool $enabled
     */
    public function __construct(array $localizedNames, $rate, $enabled)
    {
        $this->localizedNames = $localizedNames;
        $this->rate = $rate;
        $this->enabled = $enabled;
    }

    /**
     * @return array
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
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}
