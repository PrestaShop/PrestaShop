<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\AddSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject\SearchEngineId;

/**
 * Defines contract for AddSearchEngineHandler.
 */
interface AddSearchEngineHandlerInterface
{
    /**
     * @param AddSearchEngineCommand $command
     *
     * @return SearchEngineId
     */
    public function handle(AddSearchEngineCommand $command): SearchEngineId;
}
