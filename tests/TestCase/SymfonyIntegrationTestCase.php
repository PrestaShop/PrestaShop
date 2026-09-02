<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\TestCase;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Resources\DatabaseDump;

class SymfonyIntegrationTestCase extends WebTestCase
{
    use ContextMockerTrait;

    /**
     * @var KernelBrowser
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        static::mockContext();
        $this->client = self::createClient();

        // createClient already creates the kernel
        // $this->bootKernel();

        // Global var for SymfonyContainer
        global $kernel;
        $kernel = self::$kernel;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        static::resetContext();
    }

    public static function setUpBeforeClass(): void
    {
        self::restoreTestDB();
        require_once __DIR__ . '/../../config/config.inc.php';
    }

    private static function restoreTestDB(): void
    {
        DatabaseDump::restoreAllTables();
    }
}
