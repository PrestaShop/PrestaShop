<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency;

/**
 * This class provides database data by using legacy code
 * It SHOULD be replaced with new ORM code (not yet implemented)
 * It MUST NOT use too much business code, in order to be easily replaced with ORM
 *
 * @deprecated should be replaced by ORM in new code
 */
class CurrencyRepository
{

    public function getNewCurrency()
    {
        return new Currency;
    }

    /**
     * @deprecated should be replaced by ORM in new code, MUST not be complex
     *
     * @param int $id
     *
     * @return Currency
     * @throws CurrencyNotFoundException
     */
    public function getById($id)
    {
        $currency = new \Currency($id);
        if ($currency->id <= 0) {
            throw new CurrencyNotFoundException('Currency not found with id ' . $id);
        }

        $currencyModelAdapter = new Currency;
        $currencyModelAdapter->setId($currency->name);
        $currencyModelAdapter->setIsoCode($currency->iso_code);
        $currencyModelAdapter->setIsoCodeNum($currency->iso_code_num);
        $currencyModelAdapter->setConversionRate($currency->conversion_rate);
        $currencyModelAdapter->setDecimals($currency->decimals);

        return $currencyModelAdapter;
    }

    /**
     * @deprecated should be replaced by ORM in new code, MUST not be complex
     *
     * @param Currency $currencyModelAdapter
     *
     * @throws \Exception
     */
    public function add(Currency $currencyModelAdapter)
    {
        if ($currencyModelAdapter->getId() > 0) {
            throw new \Exception('Cannot add currency with given id');
        }
        $currency = new \Currency();

        $this->updateCurrencyFromModelAdapter($currency, $currencyModelAdapter);

        $currency->save();
    }

    /**
     * @deprecated should be replaced by ORM in new code, MUST not be complex
     *
     * @param Currency $currencyModelAdapter
     *
     * @throws \Exception
     */
    public function update(Currency $currencyModelAdapter)
    {
        $currency = new \Currency();
        if ($currencyModelAdapter->getId() <= 0) {
            throw new \Exception('Cannot update currency without given id');
        }

        $this->updateCurrencyFromModelAdapter($currency, $currencyModelAdapter);

        $currency->save();
    }

    /**
     * @deprecated should be replaced by ORM in new code, MUST not be complex
     *
     * @param Currency $currencyModelAdapter
     *
     * @throws \Exception
     */
    public function delete(Currency $currencyModelAdapter)
    {
        $currency = new \Currency();
        if ($currencyModelAdapter->getId() <= 0) {
            throw new \Exception('Cannot delete currency without given id');
        }

        $currency->delete();
    }

    /**
     * @deprecated should be replaced by ORM in new code, MUST not be complex
     *
     * @param \Currency $currency
     * @param Currency  $currencyModelAdapter
     */
    protected function updateCurrencyFromModelAdapter(\Currency $currency, Currency $currencyModelAdapter)
    {
        $currency->name            = $currencyModelAdapter->getId();
        $currency->iso_code        = $currencyModelAdapter->getIsoCode();
        $currency->iso_code_num    = $currencyModelAdapter->getIsoCodeNum();
        $currency->conversion_rate = $currencyModelAdapter->getConversionRate();
        $currency->decimals        = $currencyModelAdapter->getDecimals();
    }
}
