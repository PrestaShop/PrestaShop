<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;
use Tests\Resources\DatabaseDump;

/**
 * Base of the tests whose variants (debug mode, feature flags) have an impact on the Admin API kernel cache.
 * Each test must run in its own process, which starts and ends with a cleared cache, so the subclasses need the
 * runTestsInSeparateProcesses and isolatedProcess group annotations.
 */
abstract class KernelVariantApiTestCase extends ApiTestCase
{
    private static bool $debug = true;

    public static function setUpBeforeClass(): void
    {
        self::clearCache();
        DatabaseDump::restoreTables(['feature_flag']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['feature_flag']);
        self::clearCache();
    }

    protected function setUp(): void
    {
        parent::setUp();
        // The parent class setup boots the kernel: it is deferred until the debug mode of the test is known, so the
        // only container compiled is the one of the tested variant. The data sets of the variants start with $isDebug.
        $isDebug = $this->providedData()[0] ?? true;
        self::$debug = is_bool($isDebug) ? $isDebug : true;
        parent::setUpBeforeClass();
    }

    protected static function bootKernel(array $options = []): KernelInterface
    {
        return parent::bootKernel($options + ['debug' => self::$debug]);
    }

    protected static function clearCache(): void
    {
        static::ensureKernelShutdown();
        (new Filesystem())->remove(static::createKernel()->getCacheDir());
    }
}
