<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Alias\Command;

use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\SearchTerm;

/**
 * Delete all aliases by multiple given search terms.
 */
class BulkDeleteSearchTermsAliasesCommand
{
    /**
     * @var SearchTerm[]
     */
    private array $searchTerms;

    public function __construct(array $searchTerms)
    {
        foreach ($searchTerms as $searchTerm) {
            $this->searchTerms[] = new SearchTerm($searchTerm);
        }
    }

    /**
     * @return SearchTerm[]
     */
    public function getSearchTerms(): array
    {
        return $this->searchTerms;
    }
}
