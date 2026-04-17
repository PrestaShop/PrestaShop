<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\WebserviceKeyDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class WebserviceKeyFilters is responsible for providing default values for webservice account list.
 */
final class WebserviceKeyFilters extends Filters
{
    /** @var string */
    protected $filterId = WebserviceKeyDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_webservice_account',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
