<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon;

interface AddonManagerInterface
{
    public function install($source);

    public function uninstall($name);

    public function upgrade($name, $version, $source = null);

    public function enable($name);

    public function disable($name);

    public function reset($name);

    public function getError($name);
}
