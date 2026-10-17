<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

/**
 * Resource with booleans at several levels, to test the boolean casting of the query results.
 */
class BooleanCastResource
{
    public int $id;

    public bool $enabled;

    public ?bool $optional;

    public bool|int $union;

    public BooleanCastItem $item;

    /**
     * @var BooleanCastItem[]
     */
    public array $items;
}
