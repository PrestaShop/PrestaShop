<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Install;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Install\Install;
use ReflectionMethod;

/**
 * Install::finalize() used to hand the hardcoded 'admin-dev' to assets:install whenever /admin/ was
 * absent. An environment that renames the admin folder BEFORE the installer runs - the official Docker
 * image does this with PS_FOLDER_ADMIN - then failed with "The target directory ... does not exist".
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/39414
 * @see https://github.com/PrestaShop/PrestaShop/issues/40348
 */
class InstallAdminFolderDiscoveryTest extends TestCase
{
    /** @var string */
    private $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/ps-admin-discovery-' . bin2hex(random_bytes(6));
        mkdir($this->root);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->root)) {
            exec('rm -rf ' . escapeshellarg($this->root));
        }
    }

    private function findAdminFolderName(): ?string
    {
        $method = new ReflectionMethod(Install::class, 'findAdminFolderName');
        $method->setAccessible(true);

        return $method->invoke(null, $this->root);
    }

    private function makeDir(string $name, array $files): void
    {
        mkdir($this->root . '/' . $name);
        foreach ($files as $file) {
            file_put_contents($this->root . '/' . $name . '/' . $file, '<?php');
        }
    }

    public function testItFindsAnAdminFolderThatWasRenamedBeforeTheInstallerRan(): void
    {
        $this->makeDir('admin1234', ['bootstrap.php', 'init.php', 'index.php']);

        $this->assertSame('admin1234', $this->findAdminFolderName());
    }

    public function testItFindsTheDefaultAdminDevFolder(): void
    {
        $this->makeDir('admin-dev', ['bootstrap.php', 'init.php']);

        $this->assertSame('admin-dev', $this->findAdminFolderName());
    }

    /**
     * The marker has to be both files. The shop root itself ships init.php, and so do other directories,
     * so init.php alone would match the wrong place.
     */
    public function testItIgnoresDirectoriesCarryingOnlyOneOfTheTwoMarkers(): void
    {
        $this->makeDir('classes', ['init.php']);
        $this->makeDir('app', ['bootstrap.php']);
        $this->makeDir('install-dev', ['index.php', 'init.php']);

        $this->assertNull($this->findAdminFolderName());
    }

    /**
     * Nothing found must stay null rather than guessing, so finalize() keeps its existing value and the
     * original error still surfaces instead of a silently wrong folder.
     */
    public function testItReturnsNullWhenNoDirectoryLooksLikeTheAdminOne(): void
    {
        $this->makeDir('img', []);
        $this->makeDir('var', []);

        $this->assertNull($this->findAdminFolderName());
    }

    public function testItIgnoresFilesThatShareTheName(): void
    {
        file_put_contents($this->root . '/admin-dev', 'not a directory');

        $this->assertNull($this->findAdminFolderName());
    }
}
