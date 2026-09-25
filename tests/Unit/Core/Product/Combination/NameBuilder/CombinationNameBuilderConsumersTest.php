<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Product\Combination\NameBuilder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Pack\QueryHandler\GetPackedProductsHandler;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\DiscountFormDataProvider;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilder;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;
use ReflectionClass;
use ReflectionNamedType;

/**
 * A consumer that type hints the concrete builder cannot be given another one, so a module aliasing
 * CombinationNameBuilderInterface to its own implementation is silently ignored there.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/36709
 */
class CombinationNameBuilderConsumersTest extends TestCase
{
    /**
     * @return array<string, array<int, string>>
     */
    public function getConsumers(): array
    {
        return [
            'pack query handler' => [GetPackedProductsHandler::class],
            'discount form data provider' => [DiscountFormDataProvider::class],
        ];
    }

    /**
     * @dataProvider getConsumers
     */
    public function testTheBuilderIsInjectedThroughItsInterface(string $consumerClass): void
    {
        $parameter = $this->findBuilderParameter($consumerClass);
        $this->assertNotNull($parameter, sprintf(
            '%s does not take a combination name builder any more, drop it from this test.',
            $consumerClass
        ));

        $this->assertSame(
            CombinationNameBuilderInterface::class,
            $parameter,
            sprintf('%s must type hint the interface so the builder stays replaceable.', $consumerClass)
        );
    }

    /**
     * Guards the test itself: if the concrete class stopped implementing the interface, every
     * assertion above would still pass while nothing could be substituted at all.
     */
    public function testTheConcreteBuilderImplementsTheInterface(): void
    {
        $this->assertTrue(is_subclass_of(CombinationNameBuilder::class, CombinationNameBuilderInterface::class));
    }

    private function findBuilderParameter(string $consumerClass): ?string
    {
        $constructor = (new ReflectionClass($consumerClass))->getConstructor();
        if (null === $constructor) {
            return null;
        }

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType) {
                continue;
            }

            $name = $type->getName();
            if (CombinationNameBuilder::class === $name || CombinationNameBuilderInterface::class === $name) {
                return $name;
            }
        }

        return null;
    }
}
