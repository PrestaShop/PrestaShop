<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Service\Log;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\Log\AdminActivityScope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class AdminActivityScopeTest extends TestCase
{
    public function testIsDisabledWithoutMainRequest(): void
    {
        $scope = new AdminActivityScope(new RequestStack());

        self::assertFalse($scope->isEnabled());
    }

    public function testIsEnabledWhenMainRequestIsMarked(): void
    {
        $request = new Request();
        $request->attributes->set(AdminActivityScope::REQUEST_ATTRIBUTE, true);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $scope = new AdminActivityScope($requestStack);

        self::assertTrue($scope->isEnabled());
    }

    public function testIsDisabledWhenMainRequestIsNotMarked(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request());

        $scope = new AdminActivityScope($requestStack);

        self::assertFalse($scope->isEnabled());
    }

    public function testOnlyStrictTrueEnablesActivityLogging(): void
    {
        $request = new Request();
        $request->attributes->set(AdminActivityScope::REQUEST_ATTRIBUTE, 1);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $scope = new AdminActivityScope($requestStack);

        self::assertFalse($scope->isEnabled());
    }
}
