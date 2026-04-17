<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\Autoload\PrestashopAutoload;
use PrestaShop\PrestaShop\Core\Version;

require_once __DIR__ . '/../vendor/autoload.php';

define('_PS_VERSION_', Version::VERSION);

require_once _PS_CONFIG_DIR_ . 'alias.php';

PrestashopAutoload::create(_PS_ROOT_DIR_, _PS_CACHE_DIR_)
    ->register();
