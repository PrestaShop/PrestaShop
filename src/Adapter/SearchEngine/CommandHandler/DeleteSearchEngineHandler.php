<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SearchEngine\CommandHandler;

use PrestaShop\PrestaShop\Adapter\SearchEngine\AbstractSearchEngineHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\DeleteSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\CommandHandler\DeleteSearchEngineHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\DeleteSearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShopException;

/**
 * Handles command that deletes Search Engine.
 */
#[AsCommandHandler]
final class DeleteSearchEngineHandler extends AbstractSearchEngineHandler implements DeleteSearchEngineHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SearchEngineException
     */
    public function handle(DeleteSearchEngineCommand $command): void
    {
        $searchEngine = $this->getSearchEngine($command->getSearchEngineId());

        try {
            if (!$searchEngine->delete()) {
                throw new DeleteSearchEngineException(sprintf('Cannot delete Search Engine object with id "%d"', $command->getSearchEngineId()->getValue()), DeleteSearchEngineException::FAILED_DELETE);
            }
        } catch (PrestaShopException) {
            throw new SearchEngineException(sprintf('An error occurred when deleting Search Engine object with id "%d"', $command->getSearchEngineId()->getValue()));
        }
    }
}
