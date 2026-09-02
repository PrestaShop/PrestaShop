<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder;

class AbstractCatalogueFinder
{
    /**
     * Validate if an array only have strings in it.
     *
     * @param array $array
     *
     * @return bool
     */
    protected function assertIsArrayOfString(array $array): bool
    {
        return count($array) === count(array_filter($array, 'is_string'));
    }
}
