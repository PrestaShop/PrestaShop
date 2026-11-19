<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Tools;

class ToolsStringValueTest extends TestCase
{
    /**
     * @var array
     */
    private $originalPost;

    /**
     * @var array
     */
    private $originalGet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalPost = $_POST;
        $this->originalGet = $_GET;

        $_POST = [];
        $_GET = [];
        Tools::resetRequest();
        Tools::setFallbackParameters([]);
    }

    protected function tearDown(): void
    {
        $_POST = $this->originalPost;
        $_GET = $this->originalGet;
        Tools::resetRequest();
        Tools::setFallbackParameters([]);

        parent::tearDown();
    }

    public function testReturnsStringFromPost(): void
    {
        $_POST['email'] = 'test@example.com';

        $this->assertSame('test@example.com', Tools::getStringValue('email'));
    }

    public function testReturnsStringFromGet(): void
    {
        $_GET['email'] = 'test@example.com';

        $this->assertSame('test@example.com', Tools::getStringValue('email'));
    }

    public function testPostTakesPrecedenceOverGet(): void
    {
        $_POST['email'] = 'post@example.com';
        $_GET['email'] = 'get@example.com';

        $this->assertSame('post@example.com', Tools::getStringValue('email'));
    }

    public function testReturnsEmptyStringWithoutDiscardingIt(): void
    {
        $_POST['email'] = '';

        $this->assertSame('', Tools::getStringValue('email', 'fallback'));
    }

    public function testReturnsDefaultWhenParameterIsMissing(): void
    {
        $this->assertSame('', Tools::getStringValue('missing'));
        $this->assertSame('fallback', Tools::getStringValue('missing', 'fallback'));
    }

    public function testReturnsDefaultForNonStringValues(): void
    {
        foreach ([['test@example.com'], 123, false, true, 1.5, null] as $value) {
            $_POST['email'] = $value;
            $this->assertSame('', Tools::getStringValue('email'));
            $this->assertSame('fallback', Tools::getStringValue('email', 'fallback'));
        }
    }

    public function testReturnsDefaultForNonStringGetValues(): void
    {
        $_GET['email'] = ['test@example.com'];

        $this->assertSame('fallback', Tools::getStringValue('email', 'fallback'));
    }

    public function testUsesFallbackParameters(): void
    {
        Tools::setFallbackParameters(['email' => 'fallback-parameter@example.com']);

        $this->assertSame('fallback-parameter@example.com', Tools::getStringValue('email'));
    }

    public function testNonStringFallbackParameterUsesDefault(): void
    {
        Tools::setFallbackParameters(['email' => ['unexpected']]);

        $this->assertSame('fallback', Tools::getStringValue('email', 'fallback'));
    }

    public function testDoesNotChangeGetValueBehavior(): void
    {
        $_POST['email'] = ['test@example.com'];

        $this->assertSame(['test@example.com'], Tools::getValue('email'));
        $this->assertSame('', Tools::getStringValue('email'));
    }

    public function testPreservesZeroString(): void
    {
        $_POST['value'] = '0';

        $this->assertSame('0', Tools::getStringValue('value', 'fallback'));
    }

    public function testPreservesGetValueNullByteNormalization(): void
    {
        $_POST['value'] = "before\0after";

        $this->assertSame('beforeafter', Tools::getStringValue('value'));
        $this->assertSame(Tools::getValue('value'), Tools::getStringValue('value'));
    }

    public function testReadsStringFromSymfonyRequest(): void
    {
        $previousEnvironment = getenv('kernel.environment');
        putenv('kernel.environment=test');

        try {
            new Tools(new Request(['value' => 'query'], ['value' => 'post']));

            $this->assertSame('post', Tools::getStringValue('value'));
        } finally {
            Tools::resetRequest();
            $this->restoreKernelEnvironment($previousEnvironment);
        }
    }

    public function testSymfonyRequestRejectsArrayBeforeStringValidation(): void
    {
        $previousEnvironment = getenv('kernel.environment');
        putenv('kernel.environment=test');

        try {
            // getValue() evaluates the query parameter before the POST parameter.
            new Tools(new Request(['value' => ['unexpected']], ['value' => 'post']));

            $this->expectException(BadRequestException::class);
            Tools::getStringValue('value', 'fallback');
        } finally {
            Tools::resetRequest();
            $this->restoreKernelEnvironment($previousEnvironment);
        }
    }

    /**
     * @param string|false $environment
     */
    private function restoreKernelEnvironment($environment): void
    {
        if ($environment === false) {
            putenv('kernel.environment');
        } else {
            putenv('kernel.environment=' . $environment);
        }
    }
}
