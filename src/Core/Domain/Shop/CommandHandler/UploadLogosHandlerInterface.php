<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;

/**
 * Interface for service which handles UploadLogosCommand
 */
interface UploadLogosHandlerInterface
{
    /**
     * @param UploadLogosCommand $command
     */
    public function handle(UploadLogosCommand $command);
}
