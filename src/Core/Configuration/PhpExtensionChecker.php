<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Configuration;

/**
 * Class PhpExtensionChecker provides object-oriented way to check if PHP extensions are loaded.
 */
final class PhpExtensionChecker implements PhpExtensionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function loaded($extension)
    {
        return extension_loaded($extension);
    }
}
