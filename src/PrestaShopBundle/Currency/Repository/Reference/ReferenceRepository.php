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

namespace PrestaShopBundle\Currency\Repository\Reference;

use PrestaShopBundle\Currency\CurrencyFactory;
use PrestaShopBundle\Currency\Exception\InvalidArgumentException;

class ReferenceRepository implements ReferenceRepositoryInterface
{

    /**
     * @var ReferenceReaderInterface
     */
    protected $referenceReader;

    public function __construct(ReferenceReaderInterface $referenceReader)
    {
        $this->referenceReader = $referenceReader;
    }

    /**
     * Get currency data by ISO 4217 code
     *
     * @param string $isoCode
     *
     * @return array The currency data
     */
    public function getReferenceCurrencyByIsoCode($isoCode)
    {
        $currencyData = $this->referenceReader->getReferenceCurrencyByIsoCode($isoCode);

        if (empty($currencyData)) {
            throw new InvalidArgumentException('Unknown currency code : ' . $isoCode);
        }

        $factory  = new CurrencyFactory();
        $currency = $factory->setIsoCode($currencyData['isoCode'])
                            ->setNumericIsoCode($currencyData['numericIsoCode'])
                            ->setDecimalDigits($currencyData['decimalDigits'])
                            ->setDisplayName($currencyData['displayName'])
                            ->setSymbolData($currencyData['symbol'])
                            ->build();

        return $currency;
    }
}
