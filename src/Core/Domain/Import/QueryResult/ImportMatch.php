<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryResult;

/**
 * A saved data-matching configuration (named column mapping reusable across imports).
 */
final class ImportMatch
{
    /**
     * @var int
     */
    private $importMatchId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array<int, string>
     */
    private $mapping;

    /**
     * @param array<int, string> $mapping
     */
    public function __construct(int $importMatchId, string $name, array $mapping)
    {
        $this->importMatchId = $importMatchId;
        $this->name = $name;
        $this->mapping = $mapping;
    }

    public function getImportMatchId(): int
    {
        return $this->importMatchId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, string>
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }
}
