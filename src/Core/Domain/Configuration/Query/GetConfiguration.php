<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Configuration\Query;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class GetConfiguration
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var ShopId|null
     */
    private $shopId;

    /**
     * @var int|null
     */
    private $shopGroupId;

    /**
     * @param string $key
     * @param int|null $shopId
     * @param int|null $shopGroupId
     */
    public function __construct(string $key, ?int $shopId = null, ?int $shopGroupId = null)
    {
        $this->key = $key;
        $this->shopId = $shopId ? new ShopId($shopId) : null;
        $this->shopGroupId = $shopGroupId;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return ShopId|null
     */
    public function getShopId(): ?ShopId
    {
        return $this->shopId;
    }

    /**
     * @return int|null
     */
    public function getShopGroupId(): ?int
    {
        return $this->shopGroupId;
    }
}
