<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class TaxConfigurationCore
{
    private $taxCalculationMethod = [];

    /**
     * @return bool
     */
    public function includeTaxes()
    {
        if (!Configuration::get('PS_TAX')) {
            return false;
        }

        $idCustomer = (int) Context::getContext()->cookie->id_customer;
        if (!array_key_exists($idCustomer, $this->taxCalculationMethod)) {
            $this->taxCalculationMethod[$idCustomer] = !Product::getTaxCalculationMethod($idCustomer);
        }

        return $this->taxCalculationMethod[$idCustomer];
    }
}
