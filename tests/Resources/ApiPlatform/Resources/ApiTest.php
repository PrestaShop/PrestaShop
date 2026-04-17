<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreate;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSUpdate;
use PrestaShopBundle\ApiPlatform\Metadata\PaginatedList;

#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/test/unscoped/product/{productId}',
            CQRSQuery: GetProductForEditing::class,
            scopes: [],
            CQRSQueryMapping: ApiTest::QUERY_MAPPING,
        ),
        new CQRSGet(
            uriTemplate: '/test/scoped/product/{productId}',
            CQRSQuery: GetProductForEditing::class,
            scopes: ['product_read'],
            CQRSQueryMapping: ApiTest::QUERY_MAPPING,
        ),
        new CQRSGet(
            uriTemplate: '/test/experimental/product/{productId}',
            CQRSQuery: GetProductForEditing::class,
            scopes: ['experimental_scope'],
            CQRSQueryMapping: ApiTest::QUERY_MAPPING,
            experimentalOperation: true,
        ),
        new CQRSGet(
            uriTemplate: '/test/cqrs/query/not_found',
            CQRSQuery: 'PrestaShop\PrestaShop\Core\Domain\Product\Query\NotFoundQuery',
            scopes: ['filtered_query_scope'],
        ),
        new CQRSCreate(
            uriTemplate: '/test/cqrs/command/not_found',
            CQRSCommand: 'PrestaShop\PrestaShop\Core\Domain\Product\Command\NotFoundCommand',
            scopes: ['filtered_command_scope'],
        ),
        new CQRSUpdate(
            uriTemplate: '/test/cqrs/query_and_command/not_found',
            CQRSCommand: 'PrestaShop\PrestaShop\Core\Domain\Product\Command\NotFoundCommand',
            CQRSQuery: 'PrestaShop\PrestaShop\Core\Domain\Product\Query\NotFoundQuery',
            scopes: ['filtered_query_command_scope'],
        ),
        new PaginatedList(
            uriTemplate: '/test/cqrs/grid_factory/not_found',
            scopes: ['filtered_grid_factory_scope'],
            gridDataFactory: 'grid_factory_service_not_found',
        ),
    ],
)]
class ApiTest
{
    #[ApiProperty(identifier: true)]
    public int $productId;

    public string $type;

    public bool $active;

    public array $names;

    public array $descriptions;

    public const QUERY_MAPPING = [
        '[_context][shopConstraint]' => '[shopConstraint]',
        '[_context][langId]' => '[displayLanguageId]',
        '[basicInformation][localizedNames]' => '[names]',
        '[basicInformation][localizedDescriptions]' => '[descriptions]',
    ];
}
