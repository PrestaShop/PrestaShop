<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Cart;

/**
 * provide objects dealing with tax ex/in-cluded amounts
 * aims to avoid using multiple values into calculation processes.
 *
 * this class is IMMUTABLE
 */
class AmountImmutable
{
    /**
     * @var float
     */
    protected $taxIncluded = 0.0;

    /**
     * @var float
     */
    protected $taxExcluded = 0.0;

    public function __construct($taxIncluded = 0.0, $taxExcluded = 0.0)
    {
        $this->setTaxIncluded($taxIncluded);
        $this->setTaxExcluded($taxExcluded);
    }

    /**
     * @return float
     */
    public function getTaxIncluded()
    {
        return $this->taxIncluded;
    }

    /**
     * @param float $taxIncluded
     *
     * @return AmountImmutable
     */
    protected function setTaxIncluded($taxIncluded)
    {
        $this->taxIncluded = (float) $taxIncluded;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxExcluded()
    {
        return $this->taxExcluded;
    }

    /**
     * @param float $taxExcluded
     *
     * @return AmountImmutable
     */
    protected function setTaxExcluded($taxExcluded)
    {
        $this->taxExcluded = (float) $taxExcluded;

        return $this;
    }

    /**
     * Sums another amount object.
     *
     * @param AmountImmutable $amount
     *
     * @return AmountImmutable
     */
    public function add(AmountImmutable $amount)
    {
        return new static(
            $this->getTaxIncluded() + $amount->getTaxIncluded(),
            $this->getTaxExcluded() + $amount->getTaxExcluded()
        );
    }

    /**
     * Substract another amount object.
     *
     * @param AmountImmutable $amount
     *
     * @return AmountImmutable
     */
    public function sub(AmountImmutable $amount)
    {
        return new static(
            $this->getTaxIncluded() - $amount->getTaxIncluded(),
            $this->getTaxExcluded() - $amount->getTaxExcluded()
        );
    }
}
