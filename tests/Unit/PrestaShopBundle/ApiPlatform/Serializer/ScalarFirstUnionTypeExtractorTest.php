<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Serializer;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShopBundle\ApiPlatform\Serializer\ScalarFirstUnionTypeExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

class ScalarFirstUnionTypeExtractorTest extends TestCase
{
    public function testScalarMembersComeBeforeClassMembersKeepingTheirRelativeOrder(): void
    {
        // PHP reflects "int|string|bool|DecimalNumber|null" with the class first.
        $extractor = new ScalarFirstUnionTypeExtractor($this->reflectionReturning([
            new Type(Type::BUILTIN_TYPE_OBJECT, true, DecimalNumber::class),
            new Type(Type::BUILTIN_TYPE_STRING, true),
            new Type(Type::BUILTIN_TYPE_INT, true),
            new Type(Type::BUILTIN_TYPE_BOOL, true),
        ]));

        $types = $extractor->getTypes('Foo', 'bar');

        $this->assertSame(
            [Type::BUILTIN_TYPE_STRING, Type::BUILTIN_TYPE_INT, Type::BUILTIN_TYPE_BOOL, Type::BUILTIN_TYPE_OBJECT],
            array_map(static fn (Type $type): string => $type->getBuiltinType(), $types)
        );
        $this->assertSame(DecimalNumber::class, $types[3]->getClassName());
    }

    public function testSeveralClassMembersKeepTheirRelativeOrder(): void
    {
        $extractor = new ScalarFirstUnionTypeExtractor($this->reflectionReturning([
            new Type(Type::BUILTIN_TYPE_OBJECT, false, DecimalNumber::class),
            new Type(Type::BUILTIN_TYPE_OBJECT, false, DateTimeImmutable::class),
            new Type(Type::BUILTIN_TYPE_STRING, false),
        ]));

        $types = $extractor->getTypes('Foo', 'bar');

        $this->assertSame(Type::BUILTIN_TYPE_STRING, $types[0]->getBuiltinType());
        $this->assertSame(DecimalNumber::class, $types[1]->getClassName());
        $this->assertSame(DateTimeImmutable::class, $types[2]->getClassName());
    }

    /**
     * Anything that needs no reordering is left to the rest of the property_info chain.
     *
     * @dataProvider nothingToReorderProvider
     */
    public function testStaysSilentWhenThereIsNothingToReorder(?array $reflected): void
    {
        $this->assertNull((new ScalarFirstUnionTypeExtractor($this->reflectionReturning($reflected)))->getTypes('Foo', 'bar'));
    }

    public static function nothingToReorderProvider(): iterable
    {
        yield 'unknown property' => [null];
        yield 'single class type' => [[new Type(Type::BUILTIN_TYPE_OBJECT, true, DecimalNumber::class)]];
        yield 'single scalar type' => [[new Type(Type::BUILTIN_TYPE_STRING, true)]];
        yield 'scalar-only union' => [[
            new Type(Type::BUILTIN_TYPE_STRING),
            new Type(Type::BUILTIN_TYPE_INT),
            new Type(Type::BUILTIN_TYPE_FLOAT),
        ]];
        yield 'class-only union' => [[
            new Type(Type::BUILTIN_TYPE_OBJECT, false, DecimalNumber::class),
            new Type(Type::BUILTIN_TYPE_OBJECT, false, DateTimeImmutable::class),
        ]];
    }

    /**
     * End to end on a real class: PHP's canonical order puts the class first, the extractor puts it last.
     */
    public function testWorksOnTheReflectedOrderOfARealUnion(): void
    {
        $object = new class() {
            public int|string|bool|DecimalNumber|null $value;
        };

        $reflected = (new ReflectionExtractor())->getTypes($object::class, 'value');
        $this->assertSame(Type::BUILTIN_TYPE_OBJECT, $reflected[0]->getBuiltinType(), 'PHP reflects the class member first');

        $types = (new ScalarFirstUnionTypeExtractor(new ReflectionExtractor()))->getTypes($object::class, 'value');
        $this->assertSame(
            [Type::BUILTIN_TYPE_STRING, Type::BUILTIN_TYPE_INT, Type::BUILTIN_TYPE_BOOL, Type::BUILTIN_TYPE_OBJECT],
            array_map(static fn (Type $type): string => $type->getBuiltinType(), $types)
        );
    }

    /**
     * @param list<Type>|null $types
     */
    private function reflectionReturning(?array $types): PropertyTypeExtractorInterface
    {
        $extractor = $this->createMock(PropertyTypeExtractorInterface::class);
        $extractor->method('getTypes')->willReturn($types);

        return $extractor;
    }
}
