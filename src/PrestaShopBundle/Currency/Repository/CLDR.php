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

namespace PrestaShopBundle\Currency\Repository;

use InvalidArgumentException;
use PrestaShopBundle\Install\SimplexmlElement;

class CLDR implements DataSourceInterface
{
    const CLDR_ROOT = 'localization/CLDR/';
    const CLDR_MAIN = 'localization/CLDR/core/common/main/';

    protected $localeCode;

    public function __construct($localeCode)
    {
        $this->localeCode = (string)$localeCode;
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

    protected function mainPath($filename = '')
    {
        return realpath(_PS_ROOT_DIR_ . '/' . self::CLDR_MAIN . ($filename ? $filename : ''));
    }

    protected function getMainDataFilePath($langCode, $regionCode = null)
    {
        $filename = $langCode;

        if ($regionCode) {
            $filename .= '_' . $regionCode;
        }

        $filename = preg_replace('#[^_a-z-A-Z0-9]#', '', $filename);
        $filename .= '.xml';

        return $this->mainPath($filename);
    }

    /**
     * Get CLDR official xml data for a given locale tag
     *
     * The locale tag can be either an IETF tag (en-GB) or a simple language code (en)
     *
     * @param string $localeTag The locale tag.
     *
     * @return SimplexmlElement The locale data
     */
    protected function readMainData($localeTag)
    {
        $parts      = $this->getLocaleParts($localeTag);
        $langCode   = $parts['language'];
        $regionCode = isset($parts['region']) ? $parts['region'] : null;
        $filename   = $this->getMainDataFilePath($langCode, $regionCode);

        return simplexml_load_file($filename);
    }

    protected function getLocaleParts($localeTag)
    {
        $expl = explode('-', $localeTag);

        $parts = array(
            'language' => $expl[0],
        );

        if (!empty($expl[1])) {
            $parts['region'] = $expl[1];
        }

        return $parts;
    }

    /**
     * Get currency data by internal database identifier
     *
     * @param int $id
     *
     * @return array The currency data
     */
    public function getById($id)
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
    public function getByIsoCode($isoCode)
    {
        $localeCode       = $this->getLocaleCode();
        $parts            = $this->getLocaleParts($localeCode);
        $commonData       = $this->getCurrencyData($isoCode, $parts['language']);
        $regionalisedData = $this->getCurrencyData($isoCode, $localeCode);

        return array_replace_recursive($commonData, $regionalisedData);
    }

    protected function getCurrencyData($currencyCode, $localeCode)
    {
        $xmlData      = $this->readMainData($localeCode);
        $currencyData = $xmlData->xpath("/ldml/numbers/currencies/currency[@type='$currencyCode']");
        if (empty($currencyData)) {
            return array();
        }

        return $this->currencyXmlToArray($currencyData[0]);
    }

    protected function currencyXmlToArray($xmlCurrencyData)
    {
        // ISO 4217 currency code is carried by "type" attribute of <currency> tag
        // It also actually identifies the tag among others
        $currencyArray = array(
            'isoCode' => (string)$xmlCurrencyData['type'],
        );

        // Display names (depending on count)
        foreach ($xmlCurrencyData->displayName as $displayName) {
            $displayNameCount = isset($displayName['count']) ? (string)$displayName['count'] : 'default';
            $currencyArray['displayName'][$displayNameCount] = (string)$displayName;
        }

        // Symbol (full, shortened...)
        foreach ($xmlCurrencyData->symbol as $symbol) {
            $symbolType = isset($symbol['alt']) ? (string)$symbol['alt'] : 'default';
            $currencyArray['symbol'][$symbolType] = (string)$symbol;
        }

        // If no symbol at all, use ISO code.
        if (empty($currencyArray['symbol'])) {
            $currencyArray['symbol']['default'] = $currencyArray['isoCode'];
        }

        return($currencyArray);
    }
}
