<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

/**
 * Immutable set of options for an import run (truncate, reference matching, separators, …).
 */
final class ImportOptions
{
    /**
     * @var bool
     */
    private $truncate;

    /**
     * @var bool
     */
    private $matchReferences;

    /**
     * @var bool
     */
    private $forceIds;

    /**
     * @var bool
     */
    private $skipThumbnailRegeneration;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var string
     */
    private $multipleValueSeparator;

    /**
     * @var int
     */
    private $skipRows;

    public function __construct(
        bool $truncate,
        bool $matchReferences,
        bool $forceIds,
        bool $skipThumbnailRegeneration,
        string $separator,
        string $multipleValueSeparator,
        int $skipRows = 0
    ) {
        $this->truncate = $truncate;
        $this->matchReferences = $matchReferences;
        $this->forceIds = $forceIds;
        $this->skipThumbnailRegeneration = $skipThumbnailRegeneration;
        $this->separator = $separator;
        $this->multipleValueSeparator = $multipleValueSeparator;
        $this->skipRows = $skipRows;
    }

    public function truncate(): bool
    {
        return $this->truncate;
    }

    public function matchReferences(): bool
    {
        return $this->matchReferences;
    }

    public function forceIds(): bool
    {
        return $this->forceIds;
    }

    public function skipThumbnailRegeneration(): bool
    {
        return $this->skipThumbnailRegeneration;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function getMultipleValueSeparator(): string
    {
        return $this->multipleValueSeparator;
    }

    public function getSkipRows(): int
    {
        return $this->skipRows;
    }
}
