<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Exception;

/**
 * A match_ref lookup found SEVERAL products carrying the reference (product.reference
 * has a plain, non unique index). Picking one would update an arbitrary product and
 * silently discard the others, so the row must fail instead — unlike an ambiguous
 * association LINK, which only warns because it stays recoverable.
 */
class AmbiguousReferenceException extends ImportEngineException
{
    public function __construct(
        private readonly string $reference,
        private readonly int $matchCount,
    ) {
        parent::__construct(sprintf(
            'The reference "%s" matches %d products; the row was skipped to avoid updating the wrong one.',
            $reference,
            $matchCount
        ));
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getMatchCount(): int
    {
        return $this->matchCount;
    }
}
