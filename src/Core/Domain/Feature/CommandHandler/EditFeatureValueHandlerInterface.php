<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureValueCommand;

/**
 * Describes edit feature value command handler
 */
interface EditFeatureValueHandlerInterface
{
    /**
     * @param EditFeatureValueCommand $command
     */
    public function handle(EditFeatureValueCommand $command): void;
}
