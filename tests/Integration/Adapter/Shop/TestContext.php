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

namespace Tests\Integration\Adapter\Shop;

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use Shop;

/**
 * In integration tests, especially functional tests calling requests on controllers, the shop context is sometimes
 * wrongly set. This causes some bugs for services that are injected with shop context values because they are getting
 * null values where they expect a positive int.
 * This service extends the prestashop.adapter.shop.context service but forces a shop context on initialisation .
 */
class TestContext extends Context
{
    public function __construct()
    {
        Shop::setContext(Shop::CONTEXT_SHOP, 1);
    }
}
