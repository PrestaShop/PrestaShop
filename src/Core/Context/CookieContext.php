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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Cookie;
use Shop;

class CookieContext
{
    private int $shopContext = Shop::CONTEXT_ALL;
    private int $shopId;
    private int $shopGroupId;

    public function __construct(
        private readonly Cookie $cookie
    ) {
    }

    public function getShopContext(): int
    {
        $this->extractShopContext();

        return $this->shopContext;
    }

    public function getShopId(): int
    {
        $this->extractShopContext();

        return $this->shopId;
    }

    public function getShopGroupId(): int
    {
        $this->extractShopContext();

        return $this->shopGroupId;
    }

    public function getEmployeeId(): int
    {
        return $this->cookie->id_employee ? (int) $this->cookie->id_employee : 0;
    }

    private function extractShopContext(): void
    {
        if (!$this->cookie->shopContext) {
            return;
        }

        $splitShopContext = explode('-', $this->cookie->shopContext);
        if (count($splitShopContext) == 2) {
            $splitShopType = $splitShopContext[0];
            $splitShopValue = (int) $splitShopContext[1];
            if ($splitShopType == 'g') {
                $this->shopContext = Shop::CONTEXT_GROUP;
                $this->shopGroupId = $splitShopValue;
            } else {
                $this->shopContext = Shop::CONTEXT_SHOP;
                $this->shopId = $splitShopValue;
            }
        }
    }
}
