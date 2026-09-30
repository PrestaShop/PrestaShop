<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core;

interface ConfigurationInterface
{
    public function get($key);

    public function set($key, $value);

    /**
     * Set a configuration value in memory only, without persisting to database.
     * Use this for temporary flags scoped to the current process.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setTemporary(string $key, $value): void;
}
