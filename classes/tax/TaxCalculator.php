<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * TaxCaculator is responsible of the tax computation
 */
class TaxCalculatorCore
{
    /**
     * ONE_TAX_ONLY_METHOD uses one tax only
     */
    public const ONE_TAX_ONLY_METHOD = 0;

    /**
     * COMBINE_METHOD sum taxes
     * eg: 100€ * (10% + 15%).
     */
    public const COMBINE_METHOD = 1;

    /**
     * ONE_AFTER_ANOTHER_METHOD apply taxes one after another
     * eg: (100€ * 10%) * 15%.
     */
    public const ONE_AFTER_ANOTHER_METHOD = 2;

    /**
     * @var array
     */
    public $taxes;

    /**
     * @var int (COMBINE_METHOD|ONE_AFTER_ANOTHER_METHOD)
     */
    public $computation_method;

    /**
     * @param array $taxes
     * @param int $computation_method (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
     */
    public function __construct(array $taxes = [], $computation_method = TaxCalculator::COMBINE_METHOD)
    {
        // sanity check
        foreach ($taxes as $tax) {
            if (!($tax instanceof Tax)) {
                throw new Exception('Invalid Tax Object');
            }
        }

        $this->taxes = $taxes;
        $this->computation_method = (int) $computation_method;
    }

    /**
     * Compute and add the taxes to the specified price.
     *
     * @param float $price_te price tax excluded
     *
     * @return float price with taxes
     */
    public function addTaxes($price_te)
    {
        return $price_te * (1 + ($this->getTotalRate() / 100));
    }

    /**
     * Compute and remove the taxes to the specified price.
     *
     * @param float $price_ti price tax inclusive
     *
     * @return float price without taxes
     */
    public function removeTaxes($price_ti)
    {
        return $price_ti / (1 + $this->getTotalRate() / 100);
    }

    /**
     * @return float total taxes rate
     */
    public function getTotalRate()
    {
        $taxes = 0;
        if ($this->computation_method == TaxCalculator::ONE_AFTER_ANOTHER_METHOD) {
            $taxes = 1;
            foreach ($this->taxes as $tax) {
                $taxes *= (1 + (abs($tax->rate) / 100));
            }

            $taxes = $taxes - 1;
            $taxes = $taxes * 100;
        } else {
            foreach ($this->taxes as $tax) {
                $taxes += abs($tax->rate);
            }
        }

        return (float) $taxes;
    }

    public function getTaxesName()
    {
        $name = '';
        $languageId = (int) Context::getContext()->language->id;

        foreach ($this->taxes as $tax) {
            $name .= ($tax->name[$languageId] ?? '') . ' - ';
        }

        $name = rtrim($name, ' - ');

        return $name;
    }

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator.
     *
     * @param float $price_te
     *
     * @return array $taxes_amount
     */
    public function getTaxesAmount($price_te)
    {
        $taxes_amounts = [];

        foreach ($this->taxes as $tax) {
            if ($this->computation_method == TaxCalculator::ONE_AFTER_ANOTHER_METHOD) {
                $taxes_amounts[$tax->id] = $price_te * (abs($tax->rate) / 100);
                $price_te = $price_te + $taxes_amounts[$tax->id];
            } else {
                $taxes_amounts[$tax->id] = ($price_te * (abs($tax->rate) / 100));
            }
        }

        return $taxes_amounts;
    }

    /**
     * Return the total taxes amount.
     *
     * @param float $price_te
     *
     * @return float $amount
     */
    public function getTaxesTotalAmount($price_te)
    {
        $amount = 0;

        $taxes = $this->getTaxesAmount($price_te);
        foreach ($taxes as $tax) {
            $amount += $tax;
        }

        return $amount;
    }
}
