<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

/**
 * Class LocalizationPackImportConfig is value object which is responsible
 * for storing localization pack configuration for import.
 */
final class LocalizationPackImportConfig implements LocalizationPackImportConfigInterface
{
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
     * @param string $countryIso Country ISO code
     * @param array $contentToImport Content that should be impoerted (e.g states, taxes & etc)
     * @param bool $downloadPackData Whether pack data should be downloaded from prestashop.com server
     */
    public function __construct($countryIso, array $contentToImport, $downloadPackData)
    {
        $this->countryIso = (string) $countryIso;
        $this->contentToImport = $contentToImport;
        $this->downloadPackData = (bool) $downloadPackData;
    }

    /**
     * Get country ISO code.
     *
     * @return string
     */
    public function getCountryIsoCode()
    {
        return $this->countryIso;
    }

    /**
     * Get content to import.
     *
     * @return array
     */
    public function getContentToImport()
    {
        return $this->contentToImport;
    }

    /**
     * Whether pack data should be downloaded.
     *
     * @return bool
     */
    public function shouldDownloadPackData()
    {
        return $this->downloadPackData;
    }
}
