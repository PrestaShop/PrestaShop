<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform;

use PrestaShopBundle\Service\Routing\Router;
use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;

final class RouterTest extends ApiTestCase
{
    public function testTheAdminApiRouterAddsNoAdminToken(): void
    {
        $router = self::getContainer()->get('router');

        $this->assertNotInstanceOf(Router::class, $router);
        $this->assertSame($router, self::getContainer()->get('prestashop.router'));
    }
}
