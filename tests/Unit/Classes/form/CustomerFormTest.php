<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\form;

use Context;
use CustomerForm;
use CustomerFormatter;
use CustomerPersister;
use FormField;
use PHPUnit\Framework\TestCase;
use Smarty;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerFormTest extends TestCase
{
    /**
     * @dataProvider provideBackParameters
     */
    public function testFillWithNormalizesBack(array $params, ?string $expectedBack): void
    {
        $form = $this->createForm();
        $params['firstname'] = 'Jane';

        $form->fillWith($params);

        self::assertSame($expectedBack, $form->getValue('back'));
        self::assertSame('Jane', $form->getValue('firstname'));
    }

    public static function provideBackParameters(): iterable
    {
        yield 'valid string' => [['back' => 'my-account'], 'my-account'];
        yield 'URL string' => [['back' => 'https://example.com/account'], 'https://example.com/account'];
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

    private function createForm(): CustomerForm
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        // CustomerFormatter can receive additional fields via hooks. Supply a
        // hidden back field explicitly so its normalization is exercised.
        $formatter = $this->createMock(CustomerFormatter::class);
        $formatter->method('getFormat')->willReturnCallback(static function (): array {
            return [
                'firstname' => (new FormField())->setName('firstname')->setType('text'),
                'back' => (new FormField())->setName('back')->setType('hidden'),
            ];
        });

        return new CustomerForm(
            $this->createMock(Smarty::class),
            $this->createMock(Context::class),
            $translator,
            $formatter,
            $this->createMock(CustomerPersister::class),
            []
        );
    }
}
