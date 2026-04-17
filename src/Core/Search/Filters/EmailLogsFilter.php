<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\EmailLogsDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class EmailLogsFilter defines default filters for Email logs grid.
 */
final class EmailLogsFilter extends Filters
{
    /**
     * @var string
     */
    protected $filterId = EmailLogsDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_mail',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
