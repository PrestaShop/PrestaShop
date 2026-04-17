<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook\Provider;

/**
 * Defines contract for providing hooks from form types.
 */
interface HookByFormTypeProviderInterface
{
    /**
     * @param string[] $formTypes
     *
     * @return string[]
     */
    public function getHookNames(array $formTypes);
}
