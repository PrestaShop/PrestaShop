<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR\DataLayer;

use PrestaShop\PrestaShop\Core\Data\Layer\AbstractDataLayer;
use PrestaShop\PrestaShop\Core\Data\Layer\DataLayerException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyDataLayerInterface;
use PrestaShop\PrestaShop\Core\Localization\Currency\LocalizedCurrencyId;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * CurrencyCache CLDR data layer.
 *
 * This currency data layer reads and writes CLDR CurrencyData from a cache adapter
 */
final class CurrencyCache extends AbstractDataLayer implements CurrencyDataLayerInterface
{
    /**
     * Symfony Cache component adapter.
     *
     * Provides cached CurrencyData objects
     * Implements PSR-6: Cache Interface (@see http://www.php-fig.org/psr/psr-6/)
     *
     * @var AdapterInterface
     */
    protected $cache;

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
     * Actually read a CLDR CurrencyData object into the current layer.
     *
     * Might be a file access, cache read, DB select...
     *
     * @param mixed $currencyCode
     *                            The CLDR CurrencyData object identifier
     *
     * @return CurrencyData|null
     *                           The wanted CLDR CurrencyData object (null if not found)
     */
    protected function doRead($currencyCode)
    {
        $cacheItem = $this->cache->getItem($currencyCode);

        return $cacheItem->isHit()
            ? $cacheItem->get()
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function write($id, $data)
    {
        if (!($data instanceof CurrencyData)) {
            throw new LocalizationException('$data must be an instance of ' . CurrencyData::class);
        }

        return parent::write($id, $data);
    }

    /**
     * Actually write a CLDR CurrencyData object into the current layer.
     *
     * Might be a file edit, cache update, DB insert/update...
     *
     * @param LocalizedCurrencyId $currencyDataId
     *                                            The data object identifier
     * @param CurrencyData $data
     *                           The data object to be written
     *
     * @throws DataLayerException
     *                            When write fails
     */
    protected function doWrite($currencyDataId, $data)
    {
        $cacheItem = $this->cache->getItem((string) $currencyDataId);
        $cacheItem->set($data);

        $saved = $this->cache->save($cacheItem);

        if (!$saved) {
            throw new DataLayerException('Unable to persist data in cache data layer');
        }
    }
}
