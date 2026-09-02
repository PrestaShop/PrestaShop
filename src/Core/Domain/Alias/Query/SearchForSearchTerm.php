<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Alias\Query;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Class SearchAliasesForAssociation is responsible for searching aliases with particular search terms.
 */
class SearchForSearchTerm
{
    public const DEFAULT_LIMIT = 20;

    private string $searchTerm;

    private ?int $limit;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $searchTerm, ?int $limit = null)
    {
        if (null !== $limit && $limit <= 0) {
            throw new InvalidArgumentException('Search limit must be a positive integer or null');
        }
        $this->searchTerm = $searchTerm;
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
