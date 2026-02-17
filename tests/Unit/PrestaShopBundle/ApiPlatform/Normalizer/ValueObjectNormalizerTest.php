<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\Normalizer;

use DbMySQLi;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\EditApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ContactTypeChoiceProvider;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;

class ValueObjectNormalizerTest extends TestCase
{
    /**
     * @dataProvider getSupportsNormalizationValues
     *
     * @param mixed $data
     * @param bool $expectedSupport
     *
     * @return void
     */
    public function testSupportsNormalization(mixed $data, bool $expectedSupport): void
    {
        $normalizer = new ValueObjectNormalizer(new ClassMetadataFactory(new AttributeLoader()));
        $this->assertEquals($expectedSupport, $normalizer->supportsNormalization($data));
    }

    public static function getSupportsNormalizationValues(): iterable
    {
        yield 'real value object based on integer' => [new ProductId(1), true];
        yield 'real value object based on string' => [new ProductType(ProductType::TYPE_STANDARD), true];
        yield 'object with getValue but not ValueObject namespace' => [new DbMySQLi('localhost', 'root', 'root', 'prestashop', false), false];
        yield 'object without getValue method' => [new ContactTypeChoiceProvider(1), false];
    }

    /**
     * @dataProvider getNormalizationValues
     *
     * @param mixed $data
     * @param mixed $expectedNormalization
     * @param array $context
     *
     * @return void
     */
    public function testNormalize(mixed $data, mixed $expectedNormalization, array $context = []): void
    {
        $normalizer = new ValueObjectNormalizer(new ClassMetadataFactory(new AttributeLoader()));
        $this->assertEquals($expectedNormalization, $normalizer->normalize($data, null, $context));
    }

    public static function getNormalizationValues(): iterable
    {
        yield 'VO integer' => [new ProductId(1), ['productId' => 1]];
        yield 'VO string' => [new ProductType(ProductType::TYPE_STANDARD), ['productType' => ProductType::TYPE_STANDARD]];

        yield 'VO integer as scalar' => [new ProductId(1), 1, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
        yield 'VO string as scalar' => [new ProductType(ProductType::TYPE_STANDARD), ProductType::TYPE_STANDARD, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
    }

    /**
     * @dataProvider getSupportsDenormalizationValues
     *
     * @param mixed $data
     * @param bool $expectedSupport
     *
     * @return void
     */
    public function testSupportsDenormalization(mixed $data, string $type, bool $expectedSupport): void
    {
        $normalizer = new ValueObjectNormalizer(new ClassMetadataFactory(new AttributeLoader()));
        $this->assertEquals($expectedSupport, $normalizer->supportsDenormalization($data, $type));
    }

    public function getSupportsDenormalizationValues(): iterable
    {
        yield 'class with single parameter but not a ValueObject' => [1, EditApiClientCommand::class, false];

        yield 'product ID' => [['productId' => 42], ProductId::class, true];
        yield 'product ID snake case' => [['product_id' => 42], ProductId::class, true];
        yield 'product ID value' => [['value' => 42], ProductId::class, true];
        yield 'product ID integer' => [42, ProductId::class, true];
        yield 'product ID string' => ['42', ProductId::class, false];

        yield 'product type' => [['productType' => ProductType::TYPE_COMBINATIONS], ProductType::class, true];
        yield 'product type snake case' => [['product_type' => ProductType::TYPE_COMBINATIONS], ProductType::class, true];
        yield 'product type value' => [['value' => ProductType::TYPE_COMBINATIONS], ProductType::class, true];
        yield 'product type string' => [ProductType::TYPE_COMBINATIONS, ProductType::class, true];
        yield 'product type integer' => [42, ProductType::class, false];
    }

    /**
     * @dataProvider getSupportsDenormalizeValues
     *
     * @param mixed $data
     * @param string $type
     * @param mixed $expectedDenormalize
     * @param array $context
     *
     * @return void
     */
    public function testDenormalize(mixed $data, string $type, mixed $expectedDenormalize, array $context = []): void
    {
        $normalizer = new ValueObjectNormalizer(new ClassMetadataFactory(new AttributeLoader()));
        $this->assertEquals($expectedDenormalize, $normalizer->denormalize($data, $type, null, $context));
    }

    public function getSupportsDenormalizeValues(): iterable
    {
        yield 'product ID' => [['productId' => 42], ProductId::class, new ProductId(42)];
        yield 'product ID snake case' => [['product_id' => 42], ProductId::class, new ProductId(42)];
        yield 'product ID value' => [['value' => 42], ProductId::class, new ProductId(42)];
        yield 'product ID integer' => [42, ProductId::class, new ProductId(42)];

        yield 'product type' => [['productType' => ProductType::TYPE_COMBINATIONS], ProductType::class, new ProductType(ProductType::TYPE_COMBINATIONS)];
        yield 'product type snake case' => [['product_type' => ProductType::TYPE_COMBINATIONS], ProductType::class, new ProductType(ProductType::TYPE_COMBINATIONS)];
        yield 'product type value' => [['value' => ProductType::TYPE_COMBINATIONS], ProductType::class, new ProductType(ProductType::TYPE_COMBINATIONS)];
        yield 'product type string' => [ProductType::TYPE_COMBINATIONS, ProductType::class, new ProductType(ProductType::TYPE_COMBINATIONS)];

        yield 'product ID as scalar' => [['productId' => 42], ProductId::class, 42, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
        yield 'product ID snake case as scalar' => [['product_id' => 42], ProductId::class, 42, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
        yield 'product ID value as scalar' => [['value' => 42], ProductId::class, 42, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
        yield 'product ID integer as scalar' => [42, ProductId::class, 42, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];

        yield 'product type as scalar' => [['productType' => ProductType::TYPE_COMBINATIONS], ProductType::class, ProductType::TYPE_COMBINATIONS, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
        yield 'product type snake case as scalar' => [['product_type' => ProductType::TYPE_COMBINATIONS], ProductType::class, ProductType::TYPE_COMBINATIONS, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
        yield 'product type value as scalar' => [['value' => ProductType::TYPE_COMBINATIONS], ProductType::class, ProductType::TYPE_COMBINATIONS, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
        yield 'product type string as scalar' => [ProductType::TYPE_COMBINATIONS, ProductType::class, ProductType::TYPE_COMBINATIONS, [ValueObjectNormalizer::VALUE_OBJECT_RETURNED_AS_SCALAR => true]];
    }
}
