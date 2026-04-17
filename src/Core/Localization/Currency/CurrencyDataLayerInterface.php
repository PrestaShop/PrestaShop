<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

/**
 * Currency data layer classes interface.
 *
 * Describes the behavior of CurrencyDataLayer classes
 */
interface CurrencyDataLayerInterface
{
    /**
     * Read Currency data by currency code.
     *
     * @param string $currencyCode
     *                             The currency code (ISO 4217)
     *
     * @return CurrencyData
     *                      The searched currency data
     */
    public function read($currencyCode);

    /**
     * Write a Currency object into the data source.
     *
     * @param string $currencyCode
     *                             The currency code (ISO 4217)
     * @param CurrencyData $currencyData
     *                                   The currency data to write
     *
     * @return CurrencyData
     *                      The currency data to be written by the upper data layer
     */
    public function write($currencyCode, $currencyData);

    /**
     * Set the lower layer.
     * When reading data, if nothing is found then it will try to read in the lower data layer
     * When writing data, the data will also be written in the lower data layer.
     *
     * @param CurrencyDataLayerInterface $lowerLayer The lower data layer
     *
     * @return self Fluent interface
     */
    public function setLowerLayer(CurrencyDataLayerInterface $lowerLayer);
}
