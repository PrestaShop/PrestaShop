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

use InvalidArgumentException;
use PrestaShopBundle\Currency\Manager as CurrencyManager;

/**
 * Class CurrencyCollection
 *
 * This class agregates Currency objects.
 * Currencies collection is lazy loaded thanks to the Currency Manager.
 *
 * @package PrestaShopBundle\Currency
 */
class CurrencyCollection
{
    /**
     * List of lazy loaded currencies
     *
     * @var array
     */
    protected $currencies;

    /**
     * Collection's Currency Manager
     *
     * Used for lazy Currency loading
     *
     * @var CurrencyManager
     */
    protected $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        $this->setCurrencyManager($currencyManager);
    }

    public function getCurrency($identifier)
    {
        if (!isset($this->currencies[$identifier])) {
            try {
                if (is_numeric($identifier)) {
                    $currency = $this->getCurrencyManager()->getCurrency((int)$identifier);
                } else {
                    $currency = $this->getCurrencyManager()->getCurrencyByIsoCode($identifier);
                }
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException("Unknown currency : $identifier");
            }

            $this->currencies[$currency->getIsoCode()] = $this->currencies[$currency->getId()] = $currency;
        }

        return $this->currencies[$identifier];
    }

    /**
     * @return Manager
     */
    public function getCurrencyManager()
    {
        return $this->currencyManager;
    }

    /**
     * @param $currencyManager
     *
     * @return $this
     */
    public function setCurrencyManager($currencyManager)
    {
        $this->currencyManager = $currencyManager;

        return $this;
    }
}
