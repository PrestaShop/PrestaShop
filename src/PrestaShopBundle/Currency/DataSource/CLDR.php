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

namespace PrestaShopBundle\Currency\DataSource;

use PrestaShopBundle\Currency\Exception\Exception;
use PrestaShopBundle\Currency\Exception\InvalidArgumentException;
use PrestaShopBundle\Localization\CLDR\DataReaderInterface;

/**
 * Class CLDR
 * This class represents the CLDR data source. : data inputs/outputs are made from/to the official CLDR XML files.
 *
 * It implements DataSourceInterface which means it is used to read and write data from a specific place (can be local,
 * cloud, API...)
 *
 * @package PrestaShopBundle\Currency\DataSource
 */
class CLDR implements DataSourceInterface
{
    const CLDR_ROOT = 'localization/CLDR/';
    const CLDR_MAIN = 'localization/CLDR/core/common/main/';

    /**
     * The contextual locale code.. Data will be returned in this language.
     *
     * @var string
     */
    protected $localeCode;

    /**
     * The CLDR data reader (reads the CLDR data xml files)
     *
     * @var DataReaderInterface
     */
    protected $reader;

    /**
     * CLDR constructor.
     *
     * @param                     $localeCode
     * @param DataReaderInterface $reader
     */
    public function __construct($localeCode, DataReaderInterface $reader)
    {
        $this->localeCode = (string)$localeCode;
        $this->setReader($reader);
    }

    /**
     * Get data source locale code (IETF tag)
     * This locale is the locale to use when retrieving currency data.
     *
     * @return string
     *   The locale IETF tag
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * Get CLDR data reader
     *
     * @return DataReaderInterface
     *   The reader
     *
     * @throws Exception
     *   When data reader was not set
     */
    public function getReader()
    {
        if (!isset($this->reader)) {
            throw new Exception("Data reader has not been set");
        }

        return $this->reader;
    }

    /**
     * Set used locale code (IETF tag)
     *
     * @param string $localeCode
     *   The locale code to use when retrieving currency data
     *
     * @return $this
     *   Fluent interface
     */
    public function setLocaleCode($localeCode)
    {
        $this->localeCode = (string)$localeCode;

        return $this;
    }

    /**
     * Set CLDR data reader
     *
     * @param DataReaderInterface $reader
     *   The reader
     *
     * @return $this
     *   Fluent interface
     */
    public function setReader(DataReaderInterface $reader)
    {
        $this->reader = $reader;

        return $this;
    }


    /**
     * Get currency data by internal database identifier
     *
     * @param int $id
     *
     * @return array The currency data
     */
    public function getCurrencyById($id)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentException('$id must be an integer');
        }

        return null;
    }

    /**
     * Get currency data by ISO 4217 code
     *
     * @param string $isoCode
     *
     * @return array The currency data
     */
    public function getCurrencyByIsoCode($isoCode)
    {
        $currency = $this->getReader()->getCurrencyDataByIsoCode(
            $isoCode,
            $this->getLocaleCode()
        );

        return $currency;
    }
}
