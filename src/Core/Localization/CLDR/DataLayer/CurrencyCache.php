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
