<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\RemoveAllFeatureValuesFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query\GetProductFeatureValues;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreateCollection;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSDeleteCollection;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSPartialUpdateCollection;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSUpdateCollection;

/**
 * Write operations answering the feature values of the product, read back by the query after the command.
 */
#[ApiResource(
    operations: [
        // The core has no command adding feature values to a product: the set command stands for one
        new CQRSCreateCollection(
            uriTemplate: '/test/collections/products/{productId}/feature-values',
            requirements: ['productId' => '\d+'],
            CQRSCommand: SetProductFeatureValuesCommand::class,
            CQRSCommandMapping: self::COMMAND_MAPPING,
            CQRSQuery: GetProductFeatureValues::class,
            CQRSQueryMapping: self::QUERY_MAPPING,
        ),
        // Without query, the IDs returned by the command are answered
        new CQRSCreateCollection(
            uriTemplate: '/test/collections/products/{productId}/feature-value-ids',
            requirements: ['productId' => '\d+'],
            CQRSCommand: SetProductFeatureValuesCommand::class,
            CQRSCommandMapping: self::COMMAND_MAPPING,
        ),
        new CQRSUpdateCollection(
            uriTemplate: '/test/collections/products/{productId}/feature-values',
            requirements: ['productId' => '\d+'],
            CQRSCommand: SetProductFeatureValuesCommand::class,
            CQRSCommandMapping: self::COMMAND_MAPPING,
            CQRSQuery: GetProductFeatureValues::class,
            CQRSQueryMapping: self::QUERY_MAPPING,
        ),
        // The core has no command updating some feature values only: the set command stands for one
        new CQRSPartialUpdateCollection(
            uriTemplate: '/test/collections/products/{productId}/feature-values',
            requirements: ['productId' => '\d+'],
            CQRSCommand: SetProductFeatureValuesCommand::class,
            CQRSCommandMapping: self::COMMAND_MAPPING,
            CQRSQuery: GetProductFeatureValues::class,
            CQRSQueryMapping: self::QUERY_MAPPING,
        ),
        new CQRSDeleteCollection(
            uriTemplate: '/test/collections/products/{productId}/feature-values',
            requirements: ['productId' => '\d+'],
            CQRSCommand: RemoveAllFeatureValuesFromProductCommand::class,
            CQRSQuery: GetProductFeatureValues::class,
            CQRSQueryMapping: self::QUERY_MAPPING,
        ),
        // Without query and with a command returning nothing, nothing is answered
        new CQRSDeleteCollection(
            uriTemplate: '/test/collections/products/{productId}/feature-value-ids',
            requirements: ['productId' => '\d+'],
            CQRSCommand: RemoveAllFeatureValuesFromProductCommand::class,
        ),
        new CQRSDeleteCollection(
            uriTemplate: '/test/collections/products/{productId}/feature-values/bulk-delete',
            requirements: ['productId' => '\d+'],
            CQRSCommand: BulkDeleteFeatureValueCommand::class,
            CQRSQuery: GetProductFeatureValues::class,
            CQRSQueryMapping: self::QUERY_MAPPING,
            allowEmptyBody: false,
        ),
    ],
)]
class CollectionOperationsResource
{
    public int $productId;

    public int $featureId;

    public int $featureValueId;

    public bool $custom;

    /**
     * The feature values to set, only read by the creation and the updates.
     */
    #[ApiProperty(readable: false)]
    public array $featureValues;

    /**
     * The feature values to delete, only read by the bulk delete.
     *
     * @var int[]
     */
    #[ApiProperty(readable: false)]
    public array $featureValueIds;

    public const COMMAND_MAPPING = [
        '[featureValues][@index][featureId]' => '[featureValues][@index][feature_id]',
        '[featureValues][@index][featureValueId]' => '[featureValues][@index][feature_value_id]',
    ];

    public const QUERY_MAPPING = [
        '[_context][shopId]' => '[shopId]',
    ];
}
