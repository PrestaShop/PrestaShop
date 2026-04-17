<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Team\Employee\Configuration;

/**
 * Interface OptionsCheckerInterface.
 */
interface OptionsCheckerInterface
{
    /**
     * Check if employee options can be changed.
     *
     * @return bool
     */
    public function canBeChanged(): bool;
}
