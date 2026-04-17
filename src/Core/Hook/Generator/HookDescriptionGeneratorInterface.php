<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook\Generator;

use PrestaShop\PrestaShop\Core\Hook\HookDescription;

/**
 * Defines contract for generating description for hook names.
 */
interface HookDescriptionGeneratorInterface
{
    /**
     * @param string $hookName
     *
     * @return HookDescription
     */
    public function generate($hookName);
}
