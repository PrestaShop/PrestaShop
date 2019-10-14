<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency;

use Db;
use DbQuery;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidUnofficialCurrencyException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

class CurrencyCommandValidator
{
    /**
     * @var LocaleRepository
     */
    private $localeRepoCLDR;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param string $defaultLocale
     */
    public function __construct(LocaleRepository $localeRepoCLDR, $defaultLocale)
    {
        $this->localeRepoCLDR = $localeRepoCLDR;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param string $isoCode
     *
     * @throws InvalidUnofficialCurrencyException
     */
    public function assertCurrencyIsNotInReference(string $isoCode)
    {
        $defaultLocale = $this->localeRepoCLDR->getLocale($this->defaultLocale);
        $cldrCurrency = $defaultLocale->getCurrency($isoCode);
        if (null !== $cldrCurrency) {
            throw new InvalidUnofficialCurrencyException(
                sprintf(
                    'Unofficial currency with iso code "%s" is invalid because it matches a currency from CLDR database',
                    $isoCode
                )
            );
        }
    }

    /**
     * Throws an error if currency is available in the database (soft deleted currencies don't count)
     *
     * @param string $isoCode
     *
     * @throws CurrencyConstraintException
     */
    public function assertCurrencyIsNotAvailableInDatabase(string $isoCode)
    {
        $qb = new DbQuery();
        $qb
            ->select('id_currency')
            ->from('currency')
            ->where('iso_code = "' . pSQL($isoCode) . '"')
            ->where('deleted = 0')
        ;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($qb);

        if (is_numeric($result)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Currency with iso code "%s" already exists and cannot be created',
                    $isoCode
                ),
                CurrencyConstraintException::CURRENCY_ALREADY_EXISTS
            );
        }
    }
}
