<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\form;

use Context;
use CustomerLoginForm;
use CustomerLoginFormatter;
use PHPUnit\Framework\TestCase;
use Smarty;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerLoginFormTest extends TestCase
{
    /**
     * @dataProvider provideBackParameters
     */
    public function testFillWithNormalizesBack(array $params, ?string $expectedBack): void
    {
        $form = $this->createForm();
        $params['email'] = 'customer@example.com';
        $params['password'] = ' password with spaces ';

        $form->fillWith($params);

        self::assertSame($expectedBack, $form->getValue('back'));
        self::assertSame('customer@example.com', $form->getValue('email'));
        self::assertSame(' password with spaces ', $form->getValue('password'));
    }

    public static function provideBackParameters(): iterable
    {
        yield 'valid string' => [['back' => 'my-account'], 'my-account'];
        yield 'unchanged URL string' => [['back' => 'https://example.com/account?tab=orders'], 'https://example.com/account?tab=orders'];
        yield 'empty string' => [['back' => ''], ''];
        yield 'indexed array' => [['back' => ['my-account']], ''];
        yield 'associative array' => [['back' => ['page' => 'my-account']], ''];
        yield 'null' => [['back' => null], ''];
        yield 'integer' => [['back' => 123], ''];
        yield 'boolean' => [['back' => false], ''];
        yield 'absent' => [[], null];
    }

    public function testFillWithPreservesExistingBackWhenAbsent(): void
    {
        $form = $this->createForm();
        $form->fillWith(['back' => 'my-account']);

        $form->fillWith([]);

        self::assertSame('my-account', $form->getValue('back'));
    }

    private function createForm(): CustomerLoginForm
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return new CustomerLoginForm(
            $this->createMock(Smarty::class),
            $this->createMock(Context::class),
            $translator,
            new CustomerLoginFormatter($translator),
            []
        );
    }
}
