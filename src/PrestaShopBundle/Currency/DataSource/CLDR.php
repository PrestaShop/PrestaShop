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

use Exception;
use InvalidArgumentException;
use PrestaShopBundle\Localization\CLDRDataReaderInterface;

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
     * @var CLDRDataReaderInterface
     */
    protected $reader;

    /**
     * CLDR constructor.
     *
     * @param                         $localeCode
     * @param CLDRDataReaderInterface $reader
     */
    public function __construct($localeCode, CLDRDataReaderInterface $reader)
    {
        $this->localeCode = (string)$localeCode;
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

    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    public function setLocaleCode($localeCode)
    {
        $this->localeCode = (string)$localeCode;

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
        return $this->getReader()->getCurrencyByIsoCode(
            $isoCode,
            $this->getLocaleCode()
        );
    }
}
