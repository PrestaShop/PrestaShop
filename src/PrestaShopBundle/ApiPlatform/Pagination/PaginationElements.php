<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Pagination;

class PaginationElements
{
    /**
     * This is a DTO to format the paginated elements of a list.
     * The order of the parameters in the constructor cannot be changed
     * as it determines the order of the parameters in the api output.
     */
    public function __construct(
        public readonly int $totalItems,
        public readonly ?string $orderBy,
        public readonly ?string $sortOrder,
        public readonly int $limit,
        public readonly ?int $offset,
        public readonly ?array $filters,
        public readonly array $items,
    ) {
    }
}
