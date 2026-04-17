<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Email;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Result\InvalidEmail;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Email\CyrillicCharactersInEmailValidation;

class CyrillicCharactersInEmailValidationTest extends TestCase
{
    /**
     * @var CyrillicCharactersInEmailValidation
     */
    protected $validator;

    /**
     * @var EmailLexer
     */
    protected $lexer;

    protected function setUp(): void
    {
        $this->validator = new CyrillicCharactersInEmailValidation();
        $this->lexer = new EmailLexer();
    }

    public function testForEmptyWarnings(): void
    {
        $this->assertEquals([], $this->validator->getWarnings());
    }

    public function testForNonASCII(): void
    {
        $this->assertNull($this->validator->getError());
        $this->assertFalse($this->validator->isValid('é@gmail.com', $this->lexer));
        $this->assertInstanceOf(InvalidEmail::class, $this->validator->getError());
    }

    public function testForASCII(): void
    {
        $this->assertNull($this->validator->getError());
        $this->assertTrue($this->validator->isValid('test@gmail.com', $this->lexer));
        $this->assertNull($this->validator->getError());
    }
}
