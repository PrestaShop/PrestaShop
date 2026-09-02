<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Query;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\SearchShopException;

/**
 * Query responsible for getting shops for a given search term
 */
class SearchShops
{
    /**
     * @var string
     */
    private $searchTerm;

    /**
     * SearchShops constructor.
     *
     * @param string $searchTerm
     */
    public function __construct(string $searchTerm)
    {
        $this->assertSearchTermNotEmpty($searchTerm);

        $this->searchTerm = $searchTerm;
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    /**
     * @param string $searchTerm
     *
     * @throws SearchShopException
     */
    private function assertSearchTermNotEmpty(string $searchTerm): void
    {
        if (empty(trim($searchTerm))) {
            throw new SearchShopException('Search term cannot be empty.');
        }
    }
}
