<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command;

/**
 * Adds new search engine with provided data.
 */
class AddSearchEngineCommand
{
    /**
     * @var string
     */
    private $server;

    /**
     * @var string
     */
    private $queryKey;

    /**
     * @param string $server
     * @param string $queryKey
     */
    public function __construct(string $server, string $queryKey)
    {
        $this->server = $server;
        $this->queryKey = $queryKey;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getQueryKey(): string
    {
        return $this->queryKey;
    }
}
