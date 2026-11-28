<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\form;

use Country;
use CustomerAddressForm;
use CustomerAddressFormatter;
use CustomerAddressPersister;
use FormField;
use Language;
use PHPUnit\Framework\TestCase;
use Smarty;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerAddressFormTest extends TestCase
{
    /**
     * @dataProvider provideBackParameters
     */
    public function testFillWithNormalizesBack(array $params, ?string $expectedBack): void
    {
        $form = $this->createForm();
        // Matches the formatter's country so fillWith() neither loads a Country
        // from the DB nor reads the context country.
        $params['id_country'] = 1;

        $form->fillWith($params);

        self::assertSame($expectedBack, $form->getValue('back'));
    }

    public static function provideBackParameters(): iterable
    {
        yield 'valid string' => [['back' => 'addresses'], 'addresses'];
        yield 'URL string' => [['back' => 'https://example.com/addresses'], 'https://example.com/addresses'];
        yield 'empty string' => [['back' => ''], ''];
        yield 'indexed array' => [['back' => ['addresses']], ''];
        yield 'associative array' => [['back' => ['page' => 'addresses']], ''];
        yield 'null' => [['back' => null], ''];
        yield 'integer' => [['back' => 123], ''];
        yield 'boolean' => [['back' => false], ''];
        yield 'zero string' => [['back' => '0'], '0'];
        yield 'absent' => [[], null];
    }

    public function testFillWithPreservesExistingBackWhenAbsent(): void
    {
        $form = $this->createForm();
        $form->fillWith(['id_country' => 1, 'back' => 'addresses']);
        $form->fillWith(['id_country' => 1]);

        self::assertSame('addresses', $form->getValue('back'));
    }

    private function createForm(): CustomerAddressForm
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $country = $this->createMock(Country::class);
        $country->id = 1;
        $language = $this->createMock(Language::class);
        $language->id = 1;

        $formatter = $this->createMock(CustomerAddressFormatter::class);
        $formatter->method('getCountry')->willReturn($country);
        $formatter->method('getFormat')->willReturnCallback(static function (): array {
            return [
                'back' => (new FormField())->setName('back')->setType('hidden'),
            ];
        });

        return new CustomerAddressForm(
            $this->createMock(Smarty::class),
            $language,
            $translator,
            $this->createMock(CustomerAddressPersister::class),
            $formatter
        );
    }
}
