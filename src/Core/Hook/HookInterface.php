<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook;

/**
 * Interface HookInterface defines contract for hook.
 */
interface HookInterface
{
    /**
     * Get hook name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get hook parameters.
     *
     * @return array
     */
    public function getParameters();
}
