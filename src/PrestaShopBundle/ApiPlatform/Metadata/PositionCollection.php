<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\Metadata;

use Attribute;
use Symfony\Component\Serializer\Attribute\Context;

/**
 * This attribute can be added on a property in an API resource class for array containing positions,
 * the field will be expected to be an array of this form:
 *
 * [
 *     [
 *         'rowId' => 3,
 *         'newPosition' => 2,
 *     ],
 *     [
 *         'rowId' => 6,
 *         'newPosition' => 1,
 *     ],
 * ]
 *
 *  You can use the $rowIdField parameter of this Attribute to adapt the expected format (with $rowIdField = attributeId):
 *
 *  [
 *      [
 *          'attributeId' => 3,
 *          'newPosition' => 2,
 *      ],
 *      [
 *          'attributeId' => 6,
 *          'newPosition' => 1,
 *      ],
 * ]
 *
 * Thanks to this attribute the normalization process is automatized so you don't need an extra mapping, and the Open API
 * format is also adapted accordingly.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class PositionCollection extends Context
{
    public const DEFAULT_ROW_ID_FIELD = 'rowId';
    public const ROW_ID_FIELD = 'position_row_id_field';

    public function __construct(string $rowIdField = self::DEFAULT_ROW_ID_FIELD, array $context = [], array $normalizationContext = [], array $denormalizationContext = [], array|string $groups = [])
    {
        parent::__construct([self::ROW_ID_FIELD => $rowIdField] + $context, $normalizationContext, $denormalizationContext, $groups);
    }
}
