<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

/**
 * Describes add feature value command handler
 */
interface AddFeatureValueHandlerInterface
{
    /**
     * @param AddFeatureValueCommand $command
     *
     * @return FeatureValueId id of the created feature
     */
    public function handle(AddFeatureValueCommand $command): FeatureValueId;
}
