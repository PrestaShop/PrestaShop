<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Webservice;

/**
 * Interface ServerRequirementsCheckerInterface.
 */
interface ServerRequirementsCheckerInterface
{
    /**
     * Check if server meets requirements to support Webservice feature.
     *
     * @return array Errors if any
     */
    public function checkForErrors(): array;
}
