<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Db;

use Db;
use DbPDO;
use PrestaShopException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DbPDOTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    public function testInvalidQueryThrowsException(): void
    {
        $db = Db::getInstance();

        $this->assertInstanceOf(DbPDO::class, $db);

        $this->expectException(PrestaShopException::class);

        $db->executeS('SELECT * FROM', true, false);
    }
}
