<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\Command\BulkDeleteFeatureValueCommand;

interface BulkDeleteFeatureValueHandlerInterface
{
    public function handle(BulkDeleteFeatureValueCommand $command): void;
}
