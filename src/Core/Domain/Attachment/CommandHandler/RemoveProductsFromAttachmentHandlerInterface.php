<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\RemoveProductsFromAttachmentCommand;

interface RemoveProductsFromAttachmentHandlerInterface
{
    public function handle(RemoveProductsFromAttachmentCommand $command): void;
}
