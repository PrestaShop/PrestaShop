<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\AdminBar;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\AdminBar\AdminBarAction;
use PrestaShop\PrestaShop\Adapter\AdminBar\AdminBarActionUrlProvider;
use PrestaShop\PrestaShop\Adapter\Security\AdminPathProvider;

final class AdminBarActionUrlProviderTest extends TestCase
{
    /**
     * @dataProvider getInvalidEndpoints
     */
    public function testItRejectsNonLocalAdminBarEndpoints(string $endpoint): void
    {
        $provider = new AdminBarActionUrlProvider(new AdminPathProvider());

        self::assertNull($provider->getUrl(new AdminBarAction('module_edit', 'Edit module', [], $endpoint)));
    }

    public static function getInvalidEndpoints(): iterable
    {
        yield 'external URL' => ['https://example.test/admin'];
        yield 'protocol-relative URL' => ['//example.test/admin'];
        yield 'non-admin-bar path' => ['/admin/module/1'];
        yield 'path traversal' => ['/_admin-bar/module/../1'];
        yield 'query string' => ['/_admin-bar/module/1?route=admin'];
    }
}
