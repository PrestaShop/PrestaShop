<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Alias\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Alias\Repository\AliasRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\BulkDeleteSearchTermsAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\CommandHandler\BulkDeleteSearchTermsAliasesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\BulkAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\SearchTerm;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

/**
 * Handles command which deletes aliases related to search term in bulk action
 */
#[AsCommandHandler]
class BulkDeleteSearchTermsAliasesHandler extends AbstractBulkCommandHandler implements BulkDeleteSearchTermsAliasesHandlerInterface
{
    public function __construct(protected AliasRepository $aliasRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteSearchTermsAliasesCommand $command): void
    {
        $this->handleBulkAction($command->getSearchTerms(), AliasException::class);
    }

    /**
     * @param SearchTerm $term
     * @param mixed $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $term, mixed $command): void
    {
        $this->aliasRepository->deleteAliasesBySearchTerm($term);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkAliasException(
            $caughtExceptions,
            'Errors occurred during Alias bulk delete action',
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports(mixed $term): bool
    {
        return $term instanceof SearchTerm;
    }
}
