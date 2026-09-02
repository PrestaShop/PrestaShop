<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SearchEngine\CommandHandler;

use PrestaShop\PrestaShop\Adapter\SearchEngine\AbstractSearchEngineHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\BulkDeleteSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\CommandHandler\BulkDeleteSearchEngineHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\DeleteSearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShopException;

/**
 * Handles command that deletes Search Engines in bulk action.
 */
#[AsCommandHandler]
final class BulkDeleteSearchEngineHandler extends AbstractSearchEngineHandler implements BulkDeleteSearchEngineHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SearchEngineException
     */
    public function handle(BulkDeleteSearchEngineCommand $command): void
    {
        foreach ($command->getSearchEngineIds() as $searchEngineId) {
            $searchEngine = $this->getSearchEngine($searchEngineId);

            try {
                if (!$searchEngine->delete()) {
                    throw new DeleteSearchEngineException(
                        sprintf(
                            'Cannot delete Search Engine object with id "%d"',
                            $searchEngineId->getValue()
                        ),
                        DeleteSearchEngineException::FAILED_BULK_DELETE
                    );
                }
            } catch (PrestaShopException) {
                throw new SearchEngineException(sprintf('An error occurred when deleting Search Engine with id "%d"', $searchEngineId->getValue()));
            }
        }
    }
}
