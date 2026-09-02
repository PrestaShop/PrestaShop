<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources;

use PrestaShop\PrestaShop\Core\Search\Filters;

class SampleFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 51,
            'offset' => 42,
            'orderBy' => 'id_sample',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
