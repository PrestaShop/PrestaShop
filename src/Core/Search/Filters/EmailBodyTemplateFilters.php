<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\EmailBodyTemplateDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class EmailBodyTemplateFilters extends Filters
{
    protected $filterId = EmailBodyTemplateDefinitionFactory::GRID_ID;

    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'template_name',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
