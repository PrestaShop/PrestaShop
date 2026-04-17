<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;

/**
 * Interface GenerateThemeMailTemplatesHandlerInterface
 */
interface GenerateThemeMailTemplatesHandlerInterface
{
    /**
     * @param GenerateThemeMailTemplatesCommand $command
     */
    public function handle(GenerateThemeMailTemplatesCommand $command);
}
