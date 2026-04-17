<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer;

use PrestaShop\PrestaShop\Core\Data\Layer\AbstractDataLayer;
use PrestaShop\PrestaShop\Core\Data\Layer\DataLayerException;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyDataLayerInterface;
use PrestaShop\PrestaShop\Core\Localization\Currency\LocalizedCurrencyId;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Localization/CurrencyCache data layer.
 *
 * This currency data layer reads and writes Localization/CurrencyData from a cache adapter
 */
class CurrencyCache extends AbstractDataLayer implements CurrencyDataLayerInterface
{
    /**
     * Symfony Cache component adapter.
     *
     * Provides cached CurrencyData objects
     * Implements PSR-6: Cache Interface (@see http://www.php-fig.org/psr/psr-6/)
     *
     * @var AdapterInterface
     */
    private $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
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
     * Might be a file access, cache read, DB select...
     *
     * @param LocalizedCurrencyId $id The CurrencyData object identifier (currency code + locale code)
     *
     * @return CurrencyData|null The wanted CurrencyData object (null if not found)
     *
     * @throws LocalizationException When $currencyDataId is invalid
     */
    protected function doRead($id)
    {
        if (!$id instanceof LocalizedCurrencyId) {
            throw new LocalizationException('$currencyDataId must be a CurrencyDataIdentifier object');
        }

        $cacheItem = $this->cache->getItem((string) $id);

        return $cacheItem->isHit()
            ? $cacheItem->get()
            : null;
    }

    /**
     * Actually write a CurrencyData object into the current layer.
     *
     * Might be a file edit, cache update, DB insert/update...
     *
     * @param LocalizedCurrencyId $currencyDataId The data object identifier
     * @param CurrencyData $currencyData The data object to be written
     *
     * @throws DataLayerException When write fails
     * @throws LocalizationException When $currencyDataId is invalid
     */
    protected function doWrite($currencyDataId, $currencyData)
    {
        if (!$currencyDataId instanceof LocalizedCurrencyId) {
            throw new LocalizationException('$currencyDataId must be a CurrencyDataIdentifier object');
        }

        $cacheItem = $this->cache->getItem((string) $currencyDataId);
        $cacheItem->set($currencyData);

        $saved = $this->cache->save($cacheItem);

        if (!$saved) {
            throw new DataLayerException('Unable to persist data in cache data layer');
        }
    }
}
