<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Feature;

/**
 * Defines how we should access to a feature.
 */
interface FeatureInterface
{
    /**
     * @return bool
     */
    public function isUsed();

    /**
     * @return bool
     */
    public function isActive();

    public function enable();

    public function disable();

    /**
     * @param bool $status
     */
    public function update($status);
}
