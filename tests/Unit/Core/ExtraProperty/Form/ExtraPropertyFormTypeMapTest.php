<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

class ExtraPropertyFormTypeMapTest extends TestCase
{
    /**
     * @dataProvider defaultTypeProvider
     */
    public function testDefaultTypePerLogicalType(ExtraPropertyType $type, string $expectedFormType): void
    {
        [$formType] = (new ExtraPropertyFormTypeMap())->getDefaultFor($type);

        $this->assertSame($expectedFormType, $formType);
    }

    public function defaultTypeProvider(): iterable
    {
        yield 'int' => [ExtraPropertyType::INT, IntegerType::class];
        yield 'bool' => [ExtraPropertyType::BOOL, SwitchType::class];
        yield 'string' => [ExtraPropertyType::STRING, TextType::class];
        yield 'float' => [ExtraPropertyType::FLOAT, NumberType::class];
        yield 'date' => [ExtraPropertyType::DATE, DatePickerType::class];
        yield 'html' => [ExtraPropertyType::HTML, FormattedTextareaType::class];
        yield 'json' => [ExtraPropertyType::JSON, TextareaType::class];
        yield 'choice' => [ExtraPropertyType::CHOICE, ChoiceType::class];
    }

    public function testFloatDefaultsCarryTheStorageScale(): void
    {
        [, $options] = (new ExtraPropertyFormTypeMap())->getDefaultFor(ExtraPropertyType::FLOAT);

        // Storage columns are DECIMAL(20,6): the form input accepts the same precision.
        $this->assertSame(6, $options['scale']);
    }

    public function testChoiceDefaultsBuildChoicesFromEnumValues(): void
    {
        [, $options] = (new ExtraPropertyFormTypeMap())->getDefaultFor(ExtraPropertyType::CHOICE, ['a', 'b']);

        $this->assertSame(['a' => 'a', 'b' => 'b'], $options['choices']);
    }

    public function testChoiceWithoutEnumValuesYieldsEmptyChoices(): void
    {
        [, $options] = (new ExtraPropertyFormTypeMap())->getDefaultFor(ExtraPropertyType::CHOICE);

        $this->assertSame([], $options['choices']);
    }

    public function testMapCoversEveryLogicalTypeWithFormTypes(): void
    {
        $map = (new ExtraPropertyFormTypeMap())->getMap();

        foreach (ExtraPropertyType::cases() as $type) {
            $this->assertArrayHasKey($type->value, $map);
            $this->assertTrue(is_subclass_of($map[$type->value], FormTypeInterface::class));
        }
    }
}
