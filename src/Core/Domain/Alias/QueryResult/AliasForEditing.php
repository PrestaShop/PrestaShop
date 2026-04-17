<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult;

/**
 * Transfers alias data for editing.
 */
class AliasForEditing
{
    /**
     * @var string[]
     */
    private $aliases;

    /**
     * @var string
     */
    private $searchTerm;

    public function __construct(
        array $aliases,
        string $searchTerm
    ) {
        $this->aliases = $aliases;
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }
}
