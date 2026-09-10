<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Fixture for the CQRS API serializer: a property whose type is a union of scalars and a class,
 * the shape of a typed default value (ExtraPropertyDefinition::$defaultValue on the Admin API).
 */
class ScalarUnionResource
{
    public int|string|bool|DecimalNumber|null $value;

    public DecimalNumber $amount;
}
