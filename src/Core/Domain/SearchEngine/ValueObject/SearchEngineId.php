<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;

/**
 * Defines Search Engine ID with it's constraints.
 */
class SearchEngineId
{
    /**
     * @var int
     */
    private $searchEngineId;

    /**
     * @param int $searchEngineId
     *
     * @throws SearchEngineException
     */
    public function __construct(int $searchEngineId)
    {
        $this->assertIntegerIsGreaterThanZero($searchEngineId);
        $this->searchEngineId = $searchEngineId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->searchEngineId;
    }

    /**
     * @param int $searchEngineId
     *
     * @throws SearchEngineException
     */
    private function assertIntegerIsGreaterThanZero(int $searchEngineId): void
    {
        if (0 >= $searchEngineId) {
            throw new SearchEngineException(sprintf('Search engine id %d is invalid. Search engine id have to be number bigger than zero.', $searchEngineId));
        }
    }
}
