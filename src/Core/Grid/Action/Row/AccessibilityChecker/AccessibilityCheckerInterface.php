<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker;

/**
 * Interface ApplicabilityCheckerInterface.
 */
interface AccessibilityCheckerInterface
{
    /**
     * Check if record is applicable for row action.
     *
     * @param array $record
     *
     * @return bool
     */
    public function isGranted(array $record);
}
