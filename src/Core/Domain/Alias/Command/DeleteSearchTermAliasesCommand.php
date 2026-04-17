<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Alias\Command;

use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\SearchTerm;

/**
 * Delete all aliases by given search term.
 */
class DeleteSearchTermAliasesCommand
{
    private SearchTerm $searchTerm;

    public function __construct(string $searchTerm)
    {
        $this->searchTerm = new SearchTerm($searchTerm);
    }

    public function getSearchTerm(): SearchTerm
    {
        return $this->searchTerm;
    }
}
