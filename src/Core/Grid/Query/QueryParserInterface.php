<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

/**
 * Returns the executable query from a prepared one.
 */
interface QueryParserInterface
{
    /**
     * @param string $query the prepared query
     * @param array $queryParameters the query parameters
     *
     * @return string
     */
    public function parse($query, array $queryParameters);
}
