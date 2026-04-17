<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Crypto;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;

class HashingTest extends TestCase
{
    public function testSimpleCheckHashMd5()
    {
        $hashing = new Hashing();
        $salt = '2349123849231-4123';

        $this->assertTrue($hashing->checkHash('123', md5($salt . '123'), $salt));
        $this->assertFalse($hashing->checkHash('23', md5($salt . '123'), $salt));
    }

    public function testSimpleEncrypt()
    {
        $hashing = new Hashing();
        $salt = '2349123849231-4123';

        $this->assertIsString($hashing->hash('123', $salt));
    }

    public function testSimpleFirstHash()
    {
        $hashing = new Hashing();
        $salt = '2349123849231-4123';

        $this->assertTrue($hashing->isFirstHash('123', $hashing->hash('123', $salt), $salt));
        $this->assertFalse($hashing->isFirstHash('123', md5('123'), $salt));
    }
}
