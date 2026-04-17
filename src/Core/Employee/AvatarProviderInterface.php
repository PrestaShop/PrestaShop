<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Employee;

/**
 * Interface AvatarProviderInterface describes employee avatar provider.
 */
interface AvatarProviderInterface
{
    /**
     * Get default employee avatar URL.
     *
     * @return string
     */
    public function getDefaultAvatarUrl();
}
