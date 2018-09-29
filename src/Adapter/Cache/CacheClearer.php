<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Adapter\Cache;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

/**
 * Class able to clear application caches.
 */
class CacheClearer
{
    /**
     * @var CacheClearerInterface
     */
    private $entireCacheClearer;

    /**
     * @var CacheClearerInterface
     */
    private $symfonyCacheClearer;

    /**
     * @var CacheClearerInterface
     */
    private $mediaCacheClearer;

    /**
     * @var CacheClearerInterface
     */
    private $smartyCacheClearer;

    /**
     * @param CacheClearerInterface $entireCacheClearer
     * @param CacheClearerInterface $symfonyCacheClearer
     * @param CacheClearerInterface $mediaCacheClearer
     * @param CacheClearerInterface $smartyCacheClearer
     */
    public function __construct(
        CacheClearerInterface $entireCacheClearer,
        CacheClearerInterface $symfonyCacheClearer,
        CacheClearerInterface $mediaCacheClearer,
        CacheClearerInterface $smartyCacheClearer
    ) {
        $this->entireCacheClearer = $entireCacheClearer;
        $this->symfonyCacheClearer = $symfonyCacheClearer;
        $this->mediaCacheClearer = $mediaCacheClearer;
        $this->smartyCacheClearer = $smartyCacheClearer;
    }

    /**
     * Clear all application caches.
     */
    public function clearAllCaches()
    {
        $this->entireCacheClearer->clear();
    }

    /**
     * Clear Symfony cache.
     */
    public function clearSymfonyCache()
    {
        $this->symfonyCacheClearer->clear();
    }

    /**
     * Clear media cache only.
     */
    public function clearMediaCache()
    {
        $this->mediaCacheClearer->clear();
    }

    /**
     * Clear smarty cache only.
     */
    public function clearSmartyCache()
    {
        $this->smartyCacheClearer->clear();
    }
}
