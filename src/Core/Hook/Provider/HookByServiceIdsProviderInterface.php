<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook\Provider;

/**
 * Defines contract for providing hooks by using service ids.
 */
interface HookByServiceIdsProviderInterface
{
    /**
     * @param string[] $gridDefinitionServiceIds
     *
     * @return string[]
     */
    public function getHookNames(array $gridDefinitionServiceIds);
}
