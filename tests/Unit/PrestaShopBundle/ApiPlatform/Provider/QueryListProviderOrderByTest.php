<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;
use ReflectionClass;

class QueryListProviderOrderByTest extends TestCase
{
    private const FILTERS_MAPPING = [
        '[employeeId]' => '[id_employee]',
        '[enabled]' => '[active]',
    ];

    /**
     * @dataProvider getOrderBy
     */
    public function testGetSortableOrderBy(?string $orderBy, ?string $expected): void
    {
        $reflection = new ReflectionClass(QueryListProvider::class);
        $provider = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('getSortableOrderBy');

        $this->assertSame($expected, $method->invoke($provider, $orderBy, QueryListProviderOrderByTestResource::class, self::FILTERS_MAPPING));
    }

    public static function getOrderBy(): iterable
    {
        yield 'no order' => [null, null];
        yield 'mapped API field' => ['enabled', 'enabled'];
        yield 'mapped grid field' => ['id_employee', 'id_employee'];
        yield 'resource property' => ['email', 'email'];
        yield 'unknown field' => ['foo', null];
        yield 'column the resource does not expose' => ['passwd', null];
    }
}

class QueryListProviderOrderByTestResource
{
    public int $employeeId;

    public string $email;

    public bool $enabled;
}
