<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

trait NormalizeFieldTrait
{
    /**
     * @param array $rows
     *
     * @return mixed
     */
    protected function castNumericToInt($rows)
    {
        $castIdentifiersToIntegers = function (&$columnValue, $columnName) {
            if ($this->shouldCastToInt($columnName, $columnValue)) {
                $columnValue = (int) $columnValue;
            }
        };

        array_walk_recursive($rows, $castIdentifiersToIntegers);

        return $rows;
    }

    /**
     * @param array $rows
     *
     * @return mixed
     */
    protected function castIdsToArray($rows)
    {
        $castIdentifiersToArray = function (&$columnValue, $columnName) {
            if ($this->shouldCastToInt($columnName, $columnValue)) {
                $columnValue = array_map('intval', explode(',', $columnValue));
            }
        };

        array_walk_recursive($rows, $castIdentifiersToArray);

        return $rows;
    }

    /**
     * @param string $columnName
     * @param string|null $columnValue
     *
     * @return bool
     */
    private function shouldCastToInt($columnName, $columnValue)
    {
        if (null === $columnValue || 'N/A' === $columnValue) {
            return false;
        }

        return preg_match('/_id|id_|_quantity|sign|active|total_|low_stock_/', $columnName);
    }
}
