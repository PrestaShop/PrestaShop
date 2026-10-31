<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Serializer;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShopBundle\ApiPlatform\Serializer\ApiResourceCollectionTypeExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Type;
use Tests\Resources\ApiPlatform\Resources\TypedCollectionItem;

class ApiResourceCollectionTypeExtractorTest extends TestCase
{
    public function testUnionWithATypedListKeepsItsMembers(): void
    {
        $types = $this->getTypes('values');

        $this->assertCount(2, $types);
        $this->assertSame(Type::BUILTIN_TYPE_ARRAY, $types[0]->getBuiltinType());
        $this->assertSame(Type::BUILTIN_TYPE_STRING, $types[0]->getCollectionValueTypes()[0]->getBuiltinType());
        $this->assertSame(DecimalNumber::class, $types[1]->getClassName());
    }

    public function testNullableNativeTypeStaysNullable(): void
    {
        $types = $this->getTypes('nullableIds');

        $this->assertCount(1, $types);
        $this->assertTrue($types[0]->isNullable());
        $this->assertSame(Type::BUILTIN_TYPE_INT, $types[0]->getCollectionValueTypes()[0]->getBuiltinType());
    }

    public function testStaysSilentWithoutATypedList(): void
    {
        // An array whose items are not typed, and a property that is not an array
        $this->assertNull($this->getTypes('untypedValues'));
        $this->assertNull($this->getTypes('name'));
    }

    public function testStaysSilentOutsideTheApiResources(): void
    {
        $this->assertNull((new ApiResourceCollectionTypeExtractor(new PhpStanExtractor()))->getTypes(self::class, 'values'));
    }

    /**
     * @return Type[]|null
     */
    private function getTypes(string $property): ?array
    {
        return (new ApiResourceCollectionTypeExtractor(new PhpStanExtractor()))->getTypes(TypedCollectionItem::class, $property);
    }
}
