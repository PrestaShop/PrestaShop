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

use PrestaShopBundle\Currency\Exception\Exception;
use PrestaShopBundle\Currency\Exception\InvalidArgumentException;
use PrestaShopBundle\Localization\CLDR\DataReaderInterface;

/**
 * Class CLDR
 *
 * Implements ReferenceReaderInterface which means it is able to read and extract elements from reference data.
 * Here, CLDR reference data will be read. This data comes from xml files.
 *
 * @package PrestaShopBundle\Currency\Repository\Reference
 */
class CLDR implements ReferenceReaderInterface
{
    const CLDR_ROOT = 'localization/CLDR/';
    const CLDR_MAIN = 'localization/CLDR/core/common/main/';

    /**
     * The CLDR data reader (reads the CLDR data xml files)
     *
     * @var DataReaderInterface
     */
    protected $reader;

    /**
     * CLDR constructor.
     *
     * @param DataReaderInterface $reader
     */
    public function __construct(DataReaderInterface $reader)
    {
        $this->setReader($reader);
    }

    public function getReader()
    {
        if (!isset($this->reader)) {
            throw new Exception("Data reader has not been set");
        }

        return $this->reader;
    }

    public function setReader($reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * Get currency data by ISO 4217 code
     *
     * @param string $isoCode
     *   Requested currency code
     *
     * @param        $localeCode
     *   Locale to use to retrieve currency data
     *
     * @return array
     *   The currency data
     */
    public function getReferenceCurrencyByIsoCode($isoCode, $localeCode)
    {
        $currency = $this->getReader()->getCurrencyByIsoCode(
            $isoCode,
            $localeCode
        );

        return $currency;
    }
}
