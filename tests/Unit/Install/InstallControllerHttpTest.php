<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Install;

use InstallControllerHttp;
use PHPUnit\Framework\TestCase;

/**
 * The installer reports the last PHP error when a step fails without a message of its own.
 * Building the Symfony container, which every install does, raises deprecations, so that fallback
 * must not mistake one of them for the cause of the failure.
 */
class InstallControllerHttpTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../../../install-dev/classes/controllerHttp.php';
    }

    /**
     * This is the reported case: PrestaShop's own service definitions reference the deprecated
     * "prestashop.adapter.cache.clearer.symfony_cache_clearer" alias, so a real install has an
     * E_USER_DEPRECATED sitting in error_get_last() by the time any step can fail.
     */
    public function testADeprecationIsNotReportedAsTheCauseOfAFailure(): void
    {
        $last = $this->raiseThroughPhpsOwnErrorHandler(
            'Since PrestaShop\PrestaShop 9: The "prestashop.adapter.cache.clearer.symfony_cache_clearer" service alias is deprecated.',
            E_USER_DEPRECATED
        );

        $this->assertSame(E_USER_DEPRECATED, $last['type'], 'the deprecation must really be the last error, or this test proves nothing');

        $this->assertSame('', InstallControllerHttp::getLastFatalError());
    }

    public function testAWarningIsNotReportedAsTheCauseOfAFailure(): void
    {
        $last = $this->raiseThroughPhpsOwnErrorHandler('some warning', E_USER_WARNING);

        $this->assertSame(E_USER_WARNING, $last['type'], 'the warning must really be the last error, or this test proves nothing');

        $this->assertSame('', InstallControllerHttp::getLastFatalError());
    }

    /**
     * PHPUnit installs its own error handler, and a userland handler that swallows an error leaves
     * error_get_last() untouched, so raising one here would test nothing. Dropping to PHP's own
     * handler for the call is what makes this the same code path a real install takes.
     *
     * @return array the recorded last error
     */
    private function raiseThroughPhpsOwnErrorHandler(string $message, int $type): array
    {
        set_error_handler(null);

        try {
            @trigger_error($message, $type);

            return error_get_last();
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @dataProvider provideNonFatalErrorTypes
     */
    public function testNonFatalErrorTypesAreDiscarded(int $type): void
    {
        $this->assertSame('', InstallControllerHttp::getLastFatalError(['type' => $type, 'message' => 'x', 'file' => 'f', 'line' => 1]));
    }

    public static function provideNonFatalErrorTypes(): iterable
    {
        yield 'deprecated' => [E_DEPRECATED];
        yield 'user deprecated' => [E_USER_DEPRECATED];
        yield 'notice' => [E_NOTICE];
        yield 'user notice' => [E_USER_NOTICE];
        yield 'warning' => [E_WARNING];
        yield 'user warning' => [E_USER_WARNING];
    }

    /**
     * @dataProvider provideFatalErrorTypes
     */
    public function testFatalErrorTypesAreReported(int $type): void
    {
        $message = InstallControllerHttp::getLastFatalError([
            'type' => $type,
            'message' => 'Allowed memory size exhausted',
            'file' => '/var/www/html/install-dev/index.php',
            'line' => 42,
        ]);

        $this->assertStringContainsString('Allowed memory size exhausted', $message);
        $this->assertStringContainsString('/var/www/html/install-dev/index.php', $message);
    }

    public static function provideFatalErrorTypes(): iterable
    {
        yield 'error' => [E_ERROR];
        yield 'parse' => [E_PARSE];
        yield 'core error' => [E_CORE_ERROR];
        yield 'compile error' => [E_COMPILE_ERROR];
        yield 'user error' => [E_USER_ERROR];
        yield 'recoverable error' => [E_RECOVERABLE_ERROR];
    }

    public function testNoErrorAtAllYieldsNoMessage(): void
    {
        $this->assertSame('', InstallControllerHttp::getLastFatalError([]));
    }

    /**
     * A step that dies fatally never reaches ajaxJsonAnswer(), so the browser gets an empty body with
     * status 200 and shows "HTTP 200 - parsererror -", which names nothing. The shutdown handler emits
     * the answer the step could not.
     */
    public function testAFatalDuringAnAjaxStepStillProducesAJsonAnswer(): void
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $answer = InstallControllerHttp::buildFatalErrorAnswer([
            'type' => E_ERROR,
            'message' => 'Allowed memory size of 134217728 bytes exhausted',
            'file' => '/var/www/html/install-dev/index.php',
            'line' => 1,
        ]);

        $this->assertNotNull($answer);
        $decoded = json_decode((string) $answer, true);
        $this->assertIsArray($decoded, 'the browser parses this with dataType json, so it must decode');
        $this->assertFalse($decoded['success']);
        $this->assertStringContainsString('Allowed memory size', $decoded['message']);
    }

    public function testADeprecationDuringAnAjaxStepProducesNoAnswer(): void
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $this->assertNull(InstallControllerHttp::buildFatalErrorAnswer([
            'type' => E_USER_DEPRECATED,
            'message' => 'some deprecation',
            'file' => 'f',
            'line' => 1,
        ]));
    }

    /**
     * A normal page load renders HTML. Appending a JSON envelope to it would corrupt the page instead
     * of reporting anything.
     */
    public function testAFatalOutsideAnAjaxStepProducesNoAnswer(): void
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);

        $this->assertNull(InstallControllerHttp::buildFatalErrorAnswer([
            'type' => E_ERROR,
            'message' => 'Allowed memory size of 134217728 bytes exhausted',
            'file' => 'f',
            'line' => 1,
        ]));
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        parent::tearDown();
    }
}
