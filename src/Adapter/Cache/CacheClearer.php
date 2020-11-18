<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cache;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

/**
 * Class able to clear application caches.
 *
 * @internal
 */
class CacheClearer
{
    /**
     * @var CacheClearerInterface
     */
    private $cacheClearerChain;

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
     * @param CacheClearerInterface $cacheClearerChain
     * @param CacheClearerInterface $symfonyCacheClearer
     * @param CacheClearerInterface $mediaCacheClearer
     * @param CacheClearerInterface $smartyCacheClearer
     */
    public function __construct(
        CacheClearerInterface $cacheClearerChain,
        CacheClearerInterface $symfonyCacheClearer,
        CacheClearerInterface $mediaCacheClearer,
        CacheClearerInterface $smartyCacheClearer
    ) {
        $this->cacheClearerChain = $cacheClearerChain;
        $this->symfonyCacheClearer = $symfonyCacheClearer;
        $this->mediaCacheClearer = $mediaCacheClearer;
        $this->smartyCacheClearer = $smartyCacheClearer;
    }

    /**
     * Clear all application caches.
     *
     * @deprecated since 1.7.6. Use CacheClearerChain instead.
     */
    public function clearAllCaches()
    {
        @trigger_error(
            'Deprecated since 1.7.6, to be removed in 1.8. Use CacheClearerChain instead.',
            E_USER_DEPRECATED
        );

        $this->cacheClearerChain->clear();
    }

    /**
     * Clear Symfony cache.
     *
     * @deprecated since 1.7.6. Use SymfonyCacheClearer instead.
     */
    public function clearSymfonyCache()
    {
        @trigger_error(
            'Deprecated since 1.7.6, to be removed in 1.8. Use SymfonyCacheClearer instead.',
            E_USER_DEPRECATED
        );

        $this->symfonyCacheClearer->clear();
    }

    /**
     * Clear media cache only.
     *
     * @deprecated since 1.7.6. Use MediaCacheClearer instead.
     */
    public function clearMediaCache()
    {
        @trigger_error(
            'Deprecated since 1.7.6, to be removed in 1.8. Use MediaCacheClearer instead.',
            E_USER_DEPRECATED
        );

        $this->mediaCacheClearer->clear();
    }

    /**
     * Clear smarty cache only.
     *
     * @deprecated since 1.7.6. Use SmartyCacheClearer instead.
     */
    public function clearSmartyCache()
    {
        @trigger_error(
            'Deprecated since 1.7.6, to be removed in 1.8. Use SmartyCacheClearer instead.',
            E_USER_DEPRECATED
        );

        $this->smartyCacheClearer->clear();
    }
}
