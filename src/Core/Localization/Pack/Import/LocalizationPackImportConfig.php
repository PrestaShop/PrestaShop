<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

/**
 * Class LocalizationPackImportConfig
 */
class LocalizationPackImportConfig
{
    /**
     * Available content to import options
     */
    const CONTENT_STATES = 'states';
    const CONTENT_TAXES = 'taxes';
    const CONTENT_CURRENCIES = 'currencies';
    const CONTENT_LANGUAGES = 'languages';
    const CONTENT_UNITS = 'units';
    const CONTENT_GROUPS = 'groups';

    /**
     * @var string
     */
    private $countryIso;

    /**
     * @var array
     */
    private $contentToImport;

    /**
     * @var bool
     */
    private $downloadPackData;

    /**
     * @param string $countryIso       Country ISO code
     * @param array  $contentToImport  Content that should be impoerted (e.g states, taxes & etc)
     * @param bool   $downloadPackData Whether pack data should be downloaded from prestashop.com server
     */
    public function __construct($countryIso, array $contentToImport, $downloadPackData)
    {
        $this->countryIso = (string) $countryIso;
        $this->contentToImport = $contentToImport;
        $this->downloadPackData = (bool) $downloadPackData;
    }

    /**
     * Get country ISO code
     *
     * @return string
     */
    public function getCountryIsoCode()
    {
        return $this->countryIso;
    }

    /**
     * Get content to import
     *
     * @return array
     */
    public function getContentToImport()
    {
        return $this->contentToImport;
    }

    /**
     * Whether pack data should be downloaded
     *
     * @return bool
     */
    public function shouldDownloadPackData()
    {
        return $this->downloadPackData;
    }
}
