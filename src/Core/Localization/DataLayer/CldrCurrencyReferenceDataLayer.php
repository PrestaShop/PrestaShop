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

namespace PrestaShop\PrestaShop\Core\Localization\DataLayer;

use PrestaShop\PrestaShop\Core\Data\Layer\AbstractDataLayer;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

/**
 * Currency reference data layer
 *
 * Provides reference data for currencies...
 * Data comes from CLDR official data files, and is read only.
 */
class CldrCurrencyReferenceDataLayer extends AbstractDataLayer implements CldrCurrencyDataLayerInterface
{
    /**
     * CLDR locale repository
     *
     * Provides LocaleData objects
     *
     * @var LocaleRepository
     */
    protected $cldrLocaleRepository;

    protected $localeCode;

    public function __construct(LocaleRepository $cldrLocaleRepository, $localeCode)
    {
        $this->cldrLocaleRepository = $cldrLocaleRepository;
        $this->localeCode           = $localeCode;
    }

    /**
     * @inheritdoc
     */
    public function setLowerLayer(CldrCurrencyDataLayerInterface $lowerLayer)
    {
        $this->lowerDataLayer = $lowerLayer;

        return $this;
    }

    /**
     * Actually read a CurrencyData object into the current layer
     *
     * Data is read from official CLDR files (via the CLDR LocaleRepository)
     *
     * @param string $currencyCode
     *  The CurrencyData object identifier
     *
     * @return CurrencyData|null
     *  The wanted CurrencyData object (null if not found)
     */
    protected function doRead($currencyCode)
    {
        $cldrLocale = $this->cldrLocaleRepository->getLocale($this->localeCode);

        if (empty($cldrLocale)) {
            return null;
        }

        return $cldrLocale->getCurrency($currencyCode);
    }

    /**
     * CLDR files are read only. Nothing can be written there.
     *
     * @param string $localeCode
     *  The LocaleData object identifier
     *
     * @param LocaleData $data
     *  The LocaleData object to be written
     *
     * @return void
     */
    protected function doWrite($localeCode, $data)
    {
        // Nothing.
    }
}
