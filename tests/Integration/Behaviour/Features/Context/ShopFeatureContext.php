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

namespace Tests\Integration\Behaviour\Features\Context;

use RuntimeException;
use Shop;

class ShopFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var Shop[]
     */
    private $shopRegistry = [];

    /**
     * @Given shop :reference with name :shopName exists
     */
    public function shopWithNameExists($reference, $shopName)
    {
        $shopId = Shop::getIdByName($shopName);

        if (false === $shopId) {
            throw new RuntimeException(sprintf('Shop with name "%s" does not exist', $shopName));
        }

        $this->shopRegistry[$reference] = new Shop($shopId);
    }

    /**
     * @param string $reference
     *
     * @return Shop
     */
    public function getShopFromRegistry($reference)
    {
        if (!isset($this->shopRegistry[$reference])) {
            throw new RuntimeException(sprintf('Shop "%s" does not exist in registry', $reference));
        }

        return $this->shopRegistry[$reference];
    }
}
