<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Search\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Search\Command\SearchIndexationCommand;
use PrestaShop\PrestaShop\Core\Domain\Search\Exception\SearchIndexationException;

/**
 * Defines contract for search indexation handler.
 */
interface SearchIndexationHandlerInterface
{
    /**
     * @throws SearchIndexationException
     */
    public function handle(SearchIndexationCommand $command): void;
}
