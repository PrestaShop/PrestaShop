<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tag\Command\AddTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;

interface AddTagCommandHandlerInterface
{
    public function handle(AddTagCommand $command): TagId;
}
