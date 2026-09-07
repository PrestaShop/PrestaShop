<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSPaginate;

/**
 * Test twin of the ps_apiresources CombinationList resource: a CQRS-paginated list (no
 * grid behind it). Like the real resource, no property carries
 * #[ApiProperty(identifier: true)] — flagging combinationId would break the {productId}
 * URI-variable mapping (API Platform's ReadListener then 404s the collection). Item ids
 * are therefore resolved by the extra-property heuristic from the LOGICAL entity name:
 * 'combination' → combinationId. Exercises the enrichment path for lists where the grid
 * never runs — every associated property is batch-read and injected inline at the item
 * root.
 */
#[ApiResource(
    operations: [
        new CQRSPaginate(
            uriTemplate: '/test/extra-property/products/{productId}/combinations',
            CQRSQuery: GetEditableCombinationsList::class,
            scopes: ['product_read'],
            CQRSQueryMapping: [
                '[_context][langId]' => '[languageId]',
                '[_context][shopConstraint]' => '[shopConstraint]',
            ],
            ApiResourceMapping: [
                '[combinationName]' => '[name]',
            ],
            filtersClass: ProductCombinationFilters::class,
            filtersMapping: [
                '[_context][shopId]' => '[shopId]',
            ],
            itemsField: 'combinations',
            countField: 'totalCombinationsCount',
        ),
    ],
)]
class ExtraPropertyCombinationList
{
    public int $productId;

    public int $combinationId;

    public string $name;

    public string $reference;
}
