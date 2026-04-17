<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureCommand;

/**
 * Describes a service that handles feature edit command.
 */
interface EditFeatureHandlerInterface
{
    /**
     * @param EditFeatureCommand $command
     */
    public function handle(EditFeatureCommand $command);
}
