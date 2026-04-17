<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * A TaxManager define a way to retrieve tax.
 */
interface TaxManagerInterface
{
    /**
     * This method determine if the tax manager is available for the specified address.
     *
     * @param Address $address
     *
     * @return bool
     */
    public static function isAvailableForThisAddress(Address $address);

    /**
     * Return the tax calculator associated to this address.
     *
     * @return TaxCalculator
     */
    public function getTaxCalculator();
}
