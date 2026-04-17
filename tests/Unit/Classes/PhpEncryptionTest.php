<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PhpEncryption;
use PHPUnit\Framework\TestCase;

class PhpEncryptionTest extends TestCase
{
    /**
     * @var string
     */
    private const FOO = 'foo';
    /**
     * @var PhpEncryption
     */
    private $engine;

    protected function setUp(): void
    {
        $randomKey = PhpEncryption::createNewRandomKey();
        $this->engine = new PhpEncryption($randomKey);
    }

    public function testEncryptAndDecrypt(): void
    {
        $encryptedValue = $this->engine->encrypt(self::FOO);
        $this->assertSame(self::FOO, $this->engine->decrypt($encryptedValue));
    }
}
