<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use AdminController;
use Context;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Tools;

/**
 * Every legacy link and post-action redirect of an admin page is built on AdminController::$currentIndex.
 * While that was the bare file name `index.php`, it resolved against whatever path the page had been
 * served from, so a legacy controller reached through `.../admin-dir/index.php/?controller=...` produced
 * links to `.../admin-dir/index.php/index.php?...`, which matches no route.
 */
class AdminCurrentIndexTest extends TestCase
{
    public function testTheBaseIsAnchoredOnTheAdminDirectory(): void
    {
        $currentIndex = $this->buildCurrentIndex('AdminProducts');

        self::assertStringStartsWith(
            __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/index.php',
            $currentIndex,
            'the base must not be a bare file name resolved against the current path'
        );
        self::assertStringEndsWith('?controller=AdminProducts', $currentIndex);
    }

    /**
     * The shape of the value must not depend on how the page was reached; that is the whole point.
     */
    public function testTheBaseIsTheSameWhicheverPathServedThePage(): void
    {
        $served = [
            '/admin-dir/index.php?controller=AdminProducts',
            '/admin-dir/index.php/?controller=AdminProducts',
            '/admin-dir/?controller=AdminProducts',
        ];

        $built = [];
        foreach ($served as $requestUri) {
            $_SERVER['REQUEST_URI'] = $requestUri;
            $built[] = $this->buildCurrentIndex('AdminProducts');
        }
        unset($_SERVER['REQUEST_URI']);

        self::assertCount(1, array_unique($built), 'the base is independent of the request path');
    }

    public function testTheBackParameterIsCarriedAndEncoded(): void
    {
        $currentIndex = $this->buildCurrentIndex('AdminProducts', 'index.php?controller=AdminDashboard');

        self::assertStringContainsString('&back=' . urlencode('index.php?controller=AdminDashboard'), $currentIndex);
    }

    /**
     * Redirects are built by concatenating onto this value and handing the result to sanitizeAdminUrl().
     * The anchored shape must survive that without the admin directory or the shop's physical URI
     * appearing twice.
     */
    public function testARedirectBuiltOnItKeepsEachSegmentOnce(): void
    {
        $url = Tools::sanitizeAdminUrl($this->buildCurrentIndex('AdminProducts') . '&token=abc123');

        $adminDir = basename(_PS_ADMIN_DIR_);
        self::assertSame(1, substr_count($url, '/' . $adminDir . '/'), 'the admin directory appears once');

        $physicalUri = Context::getContext()->shop->physical_uri;
        if ($physicalUri !== '' && $physicalUri !== '/') {
            self::assertSame(1, substr_count($url, $physicalUri), 'the shop physical URI appears once');
        }

        self::assertStringNotContainsString('index.php/index.php', $url, 'this is the reported symptom');
    }

    private function buildCurrentIndex(string $controller, string $back = ''): string
    {
        $method = new ReflectionMethod(AdminController::class, 'buildCurrentIndex');
        $method->setAccessible(true);

        return (string) $method->invoke(null, $controller, $back);
    }
}
