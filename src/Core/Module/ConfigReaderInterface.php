<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module;

use PrestaShop\PrestaShop\Core\Util\ArrayFinder;

interface ConfigReaderInterface
{
    /**
     * Read file properties
     *
     * @param string $name The module name
     * @param string $isoCode The current iso code format fr_FR
     *
     * @return ArrayFinder|null
     */
    public function read(string $name, string $isoCode): ?ArrayFinder;
}
