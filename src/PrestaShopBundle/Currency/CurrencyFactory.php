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

namespace PrestaShopBundle\Currency;

/**
 * Class CurrencyFactory
 *
 * Builds Currency object from a given parameters set.
 *
 * @package PrestaShopBundle\Currency
 */
class CurrencyFactory
{
    /**
     * @var CurrencyParameters
     */
    protected $currencyParameters;

    /**
     * Builds and returns a Currency object from the provided data
     *
     * @param CurrencyParameters $parameters
     *   The requested Currency's parameters
     *
     * @return Currency
     */
    public function build(CurrencyParameters $parameters)
    {
        $parameters->validateProperties();

        return new Currency($parameters);
    }
}
