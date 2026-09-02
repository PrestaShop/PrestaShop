<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

/**
 * Describes add feature command handler
 */
interface AddFeatureHandlerInterface
{
    /**
     * @param AddFeatureCommand $command
     *
     * @return FeatureId id of the created feature
     */
    public function handle(AddFeatureCommand $command): FeatureId;
}
