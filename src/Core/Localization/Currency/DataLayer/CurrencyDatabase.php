<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer;

use Language;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Core\Data\Layer\AbstractDataLayer;
use PrestaShop\PrestaShop\Core\Data\Layer\DataLayerException;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyDataLayerInterface;
use PrestaShop\PrestaShop\Core\Localization\Currency\LocalizedCurrencyId;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

/**
 * Currency Database data layer.
 *
 * Provides and persists currency data from/into database
 */
class CurrencyDatabase extends AbstractDataLayer implements CurrencyDataLayerInterface
{
    /**
     * @var CurrencyDataProvider
     */
    protected $dataProvider;

    /**
     * This layer must be ready only, displaying a price should not change the database data
     *
     * @var bool
     */
    protected $isWritable = false;

    /**
     * @param CurrencyDataProvider $dataProvider
     */
    public function __construct(
        CurrencyDataProvider $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Set the lower layer.
     * When reading data, if nothing is found then it will try to read in the lower data layer
     * When writing data, the data will also be written in the lower data layer.
     *
     * @param CurrencyDataLayerInterface $lowerLayer The lower data layer
     *
     * @return self
     */
    public function setLowerLayer(CurrencyDataLayerInterface $lowerLayer)
    {
        $this->lowerDataLayer = $lowerLayer;

        return $this;
    }

    /**
     * Actually read a data object into the current layer.
     *
     * Data is read into database
     *
     * @param LocalizedCurrencyId $currencyDataId The CurrencyData object identifier (currency code + locale code)
     *
     * @return CurrencyData|null The wanted CurrencyData object (null if not found)
     *
     * @throws LocalizationException When $currencyDataId is invalid
     */
    protected function doRead($currencyDataId)
    {
        if (!$currencyDataId instanceof LocalizedCurrencyId) {
            throw new LocalizationException('First parameter must be an instance of ' . LocalizedCurrencyId::class);
        }

        $localeCode = $currencyDataId->getLocaleCode();
        $currencyCode = $currencyDataId->getCurrencyCode();
        $currencyEntity = $this->dataProvider->getCurrencyByIsoCodeAndLocale($currencyCode, $localeCode);

        if (null === $currencyEntity) {
            return null;
        }

        $currencyData = new CurrencyData();
        $currencyData->setIsoCode($currencyEntity->iso_code);
        $currencyData->setNumericIsoCode($currencyEntity->numeric_iso_code);
        $currencyData->setPrecision($currencyEntity->precision);
        $currencyData->setNames([$localeCode => $currencyEntity->name]);
        $currencyData->setSymbols([$localeCode => $currencyEntity->symbol]);

        $idLang = Language::getIdByLocale($localeCode, true);
        $currencyPattern = $currencyEntity->getPattern($idLang);
        if (!empty($currencyPattern)) {
            $currencyData->setPatterns([$localeCode => $currencyEntity->getPattern($idLang)]);
        }

        return $currencyData;
    }

    /**
     * Actually write a data object into the current layer
     * Here, this is a DB insert/update...
     *
     * @param LocalizedCurrencyId $currencyDataId The CurrencyData object identifier (currency code + locale code)
     * @param CurrencyData $currencyData The data object to be written
     *
     * @throws DataLayerException If something goes wrong when trying to write into DB
     * @throws LocalizationException When $currencyDataId is invalid
     */
    protected function doWrite($currencyDataId, $currencyData)
    {
        // We should not save anything in this layer. The CLDR or its Repository nor any of its layers
        // should modify the database. This could override customization added by the user with default
        // CLDR values. Any changes on the database must be managed through the backoffice and the appropriate
        // commands/handlers
    }
}
