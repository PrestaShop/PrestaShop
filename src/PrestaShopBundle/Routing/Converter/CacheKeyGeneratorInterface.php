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

namespace PrestaShopBundle\Routing\Converter;

/**
 * Interface CacheKeyGeneratorInterface is used by CacheProvider to generate
 * the key used for its cache, it allows to update the cache easily by varying the key.
 */
interface CacheKeyGeneratorInterface
{
    /**
     * Returns a string used as key for caching the legacy routes information.
     * You can vary this cache key in order to update the cache when needed.
     * (e.g: RoutingCacheKeyGenerator generates its key based on the last modification
     * date of routing files so that each modifications regenerate the cache).
     *
     * @return string
     */
    public function getCacheKey();
}
