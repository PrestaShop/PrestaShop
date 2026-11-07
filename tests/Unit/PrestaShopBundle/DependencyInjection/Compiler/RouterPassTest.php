<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\DependencyInjection\Compiler\RouterPass;
use PrestaShopBundle\Service\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RouterPassTest extends TestCase
{
    /**
     * The Admin app defines prestashop.router, so the alias has to point at it - that is what puts
     * the CSRF token in a back office URL.
     */
    public function testItAliasesTheRouterWhenTheTokenizedOneIsDefined(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('prestashop.router', new Definition(Router::class));

        (new RouterPass())->process($container);

        self::assertTrue($container->hasAlias('router'));
        self::assertSame('prestashop.router', (string) $container->getAlias('router'));
        self::assertTrue($container->getAlias('router')->isPublic());
    }

    /**
     * The front office and the Admin API do not define it, and must keep Symfony's own router:
     * aliasing here would either fail to resolve or put an admin token in URLs that have no use for
     * one, such as the Location header of an API response.
     */
    public function testItLeavesTheRouterAloneWhenTheTokenizedOneIsAbsent(): void
    {
        $container = new ContainerBuilder();

        (new RouterPass())->process($container);

        self::assertFalse($container->hasAlias('router'));
    }
}
