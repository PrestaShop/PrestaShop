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
use PrestaShop\PrestaShop\Core\Data\Layer\DataLayerException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Locale cache data layer
 *
 * Reads / writes into
 */
class LocaleCacheDataLayer extends AbstractDataLayer implements LocaleDataLayerInterface
{
    /**
     * Symfony Cache component adapter
     *
     * Implements PSR-6: Cache Interface (@see http://www.php-fig.org/psr/psr-6/)
     *
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * LocaleCacheDataLayer constructor.
     *
     * @param AdapterInterface $cache
     */
    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function setLowerLayer(LocaleDataLayerInterface $lowerLayer)
    {
        $this->lowerDataLayer = $lowerLayer;

        return $this;
    }

    /**
     * Actually read a LocaleData object into the current layer
     *
     * Might be a file access, cache read, DB select...
     *
     * @param mixed $id
     *  The LocaleData object identifier
     *
     * @return LocaleData|null
     *  The wanted LocaleData object (null if not found)
     */
    protected function doRead($id)
    {
        $cacheItem = $this->cache->getItem($id);

        return $cacheItem->isHit()
            ? $cacheItem->get()
            : null;
    }

    /**
     * @inheritDoc
     */
    public function write($id, $data)
    {
        if (!($data instanceof LocaleData)) {
            throw new LocalizationException(
                '$data must be an instance of PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData'
            );
        }

        return parent::write($id, $data);
    }

    /**
     * Actually write a LocaleData object into the current layer
     *
     * Might be a file edit, cache update, DB insert/update...
     *
     * @param mixed $id
     *  The LocaleData object identifier
     *
     * @param LocaleData $data
     *  The LocaleData object to be written
     *
     * @return void
     *
     * @throws DataLayerException
     *  When write fails
     */
    protected function doWrite($id, $data)
    {
        $cacheItem = $this->cache->getItem($id);
        $cacheItem->set($data);

        $saved = $this->cache->save($cacheItem);

        if (!$saved) {
            throw new DataLayerException('Unable to persist data in cache data layer');
        }
    }
}
