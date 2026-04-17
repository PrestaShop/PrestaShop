<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Interface for services that handles command which adds new language
 */
interface AddLanguageHandlerInterface
{
    /**
     * @param AddLanguageCommand $command
     *
     * @return LanguageId Added language's identity
     */
    public function handle(AddLanguageCommand $command);
}
