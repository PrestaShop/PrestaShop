<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util;

class Sorter
{
    public const ORDER_ASC = 'ASC';
    public const ORDER_DESC = 'DESC';

    /**
     * @param array<array<string, mixed>> $array
     * @param string $order
     * @param string ...$criterias
     *
     * @return array
     */
    public function natural(array $array, string $order, string ...$criterias): array
    {
        usort($array, function ($a, $b) use ($order, $criterias) {
            $cmp = 0;
            foreach ($criterias as $criteria) {
                if (!isset($a[$criteria]) || !isset($b[$criteria])) {
                    return 0;
                }
                $cmp = strnatcmp($a[$criteria], $b[$criteria]);
                if ($cmp !== 0) {
                    break;
                }
            }

            return static::ORDER_DESC === $order ? $cmp : $cmp * -1;
        });

        return $array;
    }
}
