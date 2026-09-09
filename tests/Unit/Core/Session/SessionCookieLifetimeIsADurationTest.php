<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Session;

use PHPUnit\Framework\TestCase;

/**
 * config.inc.php builds two different things out of the configured cookie lifetime: an absolute moment
 * for Cookie, which is what its constructor takes, and a duration in seconds for SessionHandler, whose
 * init() hands it to session_set_cookie_params(). Feeding the absolute timestamp to the second gave
 * PHPSESSID a Max-Age of roughly 1.8 billion seconds.
 *
 * The wiring lives in procedural bootstrap that cannot be executed in isolation, so the guard is on the
 * file itself: SessionHandler must not be constructed with the variable holding the absolute moment.
 */
class SessionCookieLifetimeIsADurationTest extends TestCase
{
    private const BOOTSTRAP = __DIR__ . '/../../../../config/config.inc.php';

    private function getSessionHandlerConstruction(): string
    {
        $source = file_get_contents(self::BOOTSTRAP);
        self::assertNotFalse($source, 'config.inc.php could not be read');

        $start = strpos($source, 'new SessionHandler(');
        self::assertNotFalse($start, 'config.inc.php no longer constructs a SessionHandler');

        $end = strpos($source, ');', $start);
        self::assertNotFalse($end);

        return substr($source, $start, $end - $start);
    }

    public function testTheSessionHandlerIsNotGivenTheAbsoluteExpiry(): void
    {
        $construction = $this->getSessionHandlerConstruction();

        $this->assertStringNotContainsString(
            '$cookie_lifetime',
            $construction,
            'SessionHandler received the absolute expiry Cookie takes; session_set_cookie_params() wants a duration'
        );
        $this->assertStringContainsString('$session_cookie_lifetime', $construction);
    }

    public function testTheDurationIsNotBuiltFromTheCurrentTime(): void
    {
        $source = file_get_contents(self::BOOTSTRAP);
        self::assertNotFalse($source);

        // The assignment that produces the duration must be hours times seconds, with no clock in it.
        $this->assertSame(
            1,
            preg_match('/\$session_cookie_lifetime = ([^;]+);/', $source, $matches),
            'the duration is no longer assigned in one place, so this guard cannot see it'
        );
        $this->assertStringNotContainsString(
            'time()',
            $matches[1],
            'the session lifetime was built from the clock, which makes it an absolute moment again'
        );
    }
}
