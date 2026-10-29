<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Post;
use ArrayObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShopBundle\ApiPlatform\DomainObjectDetector;
use PrestaShopBundle\ApiPlatform\OpenApi\Adapter\MultiParameterSetterAdapter;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;

class MultiParameterSetterAdapterTest extends TestCase
{
    public function testDecimalNumberParameters(): void
    {
        $adapter = new MultiParameterSetterAdapter(
            new ClassMetadataFactory(new AttributeLoader()),
            new DomainObjectDetector([], ['PrestaShop\\PrestaShop\\Core\\Domain\\']),
        );
        /** @var ArrayObject<string, mixed> $definition */
        $definition = new ArrayObject(['type' => 'object', 'properties' => []]);
        $adapter->adapt(AddDiscountCommand::class, $definition, new Post(input: ['class' => AddDiscountCommand::class]));

        // setReductionAmount(DecimalNumber $amount, int $currencyId, bool $taxIncluded)
        $this->assertEquals(new ArrayObject([
            'type' => 'object',
            'properties' => [
                'amount' => new ArrayObject(['type' => 'number', 'example' => 42.99]),
                'currencyId' => new ArrayObject(['type' => 'integer']),
                'taxIncluded' => new ArrayObject(['type' => 'boolean']),
            ],
            'required' => ['amount', 'currencyId', 'taxIncluded'],
        ]), $definition['properties']['reductionAmount']);

        // setMinimumAmount(?DecimalNumber $amount, int $currencyId = 0, bool $taxIncluded = true, bool $shippingIncluded = true)
        $this->assertEquals(new ArrayObject([
            'type' => ['object', 'null'],
            'properties' => [
                'amount' => new ArrayObject(['type' => ['number', 'null'], 'example' => 42.99]),
                'currencyId' => new ArrayObject(['type' => 'integer']),
                'taxIncluded' => new ArrayObject(['type' => 'boolean']),
                'shippingIncluded' => new ArrayObject(['type' => 'boolean']),
            ],
            'required' => ['amount'],
        ]), $definition['properties']['minimumAmount']);

        // setValidityDateRange(DateTimeImmutable $from, ?DateTimeImmutable $to = null)
        $this->assertEquals(new ArrayObject([
            'type' => 'object',
            'properties' => [
                'from' => new ArrayObject(['format' => 'date-time', 'type' => 'string', 'example' => '2025-11-05 16:02:06']),
                'to' => new ArrayObject(['format' => 'date-time', 'type' => ['string', 'null'], 'example' => '2025-11-05 16:02:06']),
            ],
            'required' => ['from'],
        ]), $definition['properties']['validityDateRange']);
    }
}
