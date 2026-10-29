<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use PrestaShop\Decimal\DecimalNumber;

class TypedCollectionItem
{
    /**
     * @var list<string>|DecimalNumber
     */
    public array|DecimalNumber $values;

    /**
     * @var int[]
     */
    public ?array $nullableIds;

    /**
     * @var array<string, mixed>|DecimalNumber
     */
    public array|DecimalNumber $untypedValues;

    public string $name;
}
