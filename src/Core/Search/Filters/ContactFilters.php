<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ContactGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class ContactFilters is responsible for providing default filter values for Contacts list.
 */
final class ContactFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = ContactGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_contact',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
