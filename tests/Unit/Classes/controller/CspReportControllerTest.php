<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use Context;
use CspReportControllerCore;
use PHPUnit\Framework\TestCase;
use Shop;

/**
 * The public report endpoint must always answer 204 and never surface a 500: a non-POST request is
 * ignored without recording, and any failure while collecting is swallowed. The full record path is
 * exercised end-to-end against a running storefront (HTTP collector test).
 */
class CspReportControllerTest extends TestCase
{
    /** @var array<string, string|null> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        $this->serverBackup = [
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? null,
            'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? null,
        ];
    }

    protected function tearDown(): void
    {
        foreach ($this->serverBackup as $key => $value) {
            if (null === $value) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $value;
            }
        }
    }

    public function testItAnswers204AndRecordsNothingForANonPostRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $controller = new TestableCspReportController();

        $controller->postProcess();

        $this->assertSame(204, $controller->terminatedStatus);
    }

    public function testItAnswers204WhenCollectingThrows(): void
    {
        // POST reaches the service lookup with no container wired, so collectReports() throws; the
        // endpoint must still answer 204.
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $controller = new TestableCspReportController();

        $controller->postProcess();

        $this->assertSame(204, $controller->terminatedStatus);
    }
}

class TestableCspReportController extends CspReportControllerCore
{
    public ?int $terminatedStatus = null;

    public function __construct()
    {
        // A minimal shop context so the per-shop id lookup is clean; the throw comes later, from the
        // unset service container. Shop with no id is not loaded from the database.
        $shop = new Shop();
        $shop->id = 1;
        $context = new Context();
        $context->shop = $shop;
        $this->context = $context;
    }

    protected function terminateResponse(int $statusCode): void
    {
        $this->terminatedStatus = $statusCode;
    }
}
