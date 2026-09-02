<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

/**
 * Interface that toggles language's status command
 */
interface ToggleLanguageStatusCommandInterface
{
    /**
     * @return bool
     */
    public function getStatus();
}
