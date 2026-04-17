<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\shop;

use Configuration;
use PHPUnit\Framework\TestCase;
use Shop;

class ShopTest extends TestCase
{
    public function testGetBaseURL(): void
    {
        $domain = Configuration::get('PS_SHOP_DOMAIN');
        $shop = new Shop();
        $shop->domain = $domain;
        $shop->physical_uri = '/';

        $this->assertEquals('http://' . $domain . __PS_BASE_URI__, $shop->getBaseURL(true, true));
        $this->assertEquals('http://' . $domain, $shop->getBaseURL(true, false));
    }
}
