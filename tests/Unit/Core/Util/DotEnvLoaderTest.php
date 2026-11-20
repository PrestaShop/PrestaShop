<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Util;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\DotEnvLoader;
use Symfony\Component\Filesystem\Filesystem;

class DotEnvLoaderTest extends TestCase
{
    private const VARIABLE = 'PS_DOT_ENV_LOADER_TEST';

    private string $directory;

    /** @var array<string, mixed> */
    private array $env;

    /** @var array<string, mixed> */
    private array $server;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/' . uniqid('DotEnvLoaderTest', true);
        mkdir($this->directory);
        $this->env = $_ENV;
        $this->server = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->env;
        $_SERVER = $this->server;
        (new Filesystem())->remove($this->directory);
    }

    public function testNothingIsLoadedWithoutAnyFile(): void
    {
        DotEnvLoader::load($this->directory . '/.env');

        $this->assertArrayNotHasKey(self::VARIABLE, $_ENV);
    }

    public function testTheEnvFileIsLoaded(): void
    {
        file_put_contents($this->directory . '/.env', self::VARIABLE . "=env\n");

        DotEnvLoader::load($this->directory . '/.env');

        $this->assertSame('env', $_ENV[self::VARIABLE]);
    }

    public function testTheLocalFileOverridesTheEnvFile(): void
    {
        file_put_contents($this->directory . '/.env', self::VARIABLE . "=env\n");
        file_put_contents($this->directory . '/.env.local', self::VARIABLE . "=local\n");

        DotEnvLoader::load($this->directory . '/.env');

        $this->assertSame('local', $_ENV[self::VARIABLE]);
    }

    public function testTheLocalFileIsLoadedWithoutTheEnvFile(): void
    {
        file_put_contents($this->directory . '/.env.local', self::VARIABLE . "=local\n");

        DotEnvLoader::load($this->directory . '/.env');

        $this->assertSame('local', $_ENV[self::VARIABLE]);
    }
}
