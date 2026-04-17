<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer;

use PrestaShop\PrestaShop\Core\Data\Layer\AbstractDataLayer;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyInterface as CldrCurrency;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository as CldrLocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyDataLayerInterface;
use PrestaShop\PrestaShop\Core\Localization\Currency\LocalizedCurrencyId;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

/**
 * Localization/CurrencyReference data layer.
 *
 * Provides reference data for currencies...
 * Data comes from CLDR official data files, and is read only.
 */
class CurrencyReference extends AbstractDataLayer implements CurrencyDataLayerInterface
{
    /**
     * CLDR locale repository.
     *
     * Provides LocaleData objects
     *
     * @var CldrLocaleRepository
     */
    protected $cldrLocaleRepository;

    public function __construct(CldrLocaleRepository $cldrLocaleRepository)
    {
        $this->cldrLocaleRepository = $cldrLocaleRepository;
        $this->isWritable = false;
    }

    /**
     * {@inheritdoc}
     */
    public function setLowerLayer(CurrencyDataLayerInterface $lowerLayer)
    {
        $this->lowerDataLayer = $lowerLayer;

        return $this;
    }

    /**
     * Actually read a CurrencyData object into the current layer.
     *
     * Data is read from official CLDR files (via the CLDR LocaleRepository)
     *
     * @param LocalizedCurrencyId $currencyDataId
     *                                            The CurrencyData object identifier
     *
     * @return CurrencyData|null
     *                           The wanted CurrencyData object (null if not found)
     *
     * @throws LocalizationException
     *                               In case of invalid $currencyDataId
     *                               Also in case of invalid type asked for symbol (but use a constant, so it is very unlikely...)
     */
    protected function doRead($currencyDataId)
    {
        if (!$currencyDataId instanceof LocalizedCurrencyId) {
            throw new LocalizationException('$currencyDataId must be a CurrencyDataIdentifier object');
        }

        $localeCode = $currencyDataId->getLocaleCode();
        $cldrLocale = $this->cldrLocaleRepository->getLocale($localeCode);

        if (empty($cldrLocale)) {
            return null;
        }

        $cldrCurrency = $cldrLocale->getCurrency($currencyDataId->getCurrencyCode());

        if (empty($cldrCurrency)) {
            return null;
        }

        $currencyData = new CurrencyData();
        $currencyData->setIsoCode($cldrCurrency->getIsoCode());
        $currencyData->setNumericIsoCode($cldrCurrency->getNumericIsoCode());
        $currencyData->setSymbols([$localeCode => $cldrCurrency->getSymbol(CldrCurrency::SYMBOL_TYPE_NARROW)]);
        $currencyData->setPrecision($cldrCurrency->getDecimalDigits());
        $currencyData->setNames([$localeCode => $cldrCurrency->getDisplayName()]);

        return $currencyData;
    }

    /**
     * CLDR files are read only. Nothing can be written there.
     *
     * @param LocalizedCurrencyId $currencyDataId
     *                                            The LocaleData object identifier
     * @param CurrencyData $currencyData
     *                                   The CurrencyData object to be written
     */
    protected function doWrite($currencyDataId, $currencyData)
    {
        // Nothing.
    }
}
