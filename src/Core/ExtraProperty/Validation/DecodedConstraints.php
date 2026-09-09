<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Outcome of a tolerant decoding: what could be read, and what had to be dropped.
 *
 * The tolerant path never throws — a corrupt registry row must not break a front-office render — so
 * it needs a way to report what it discarded without hiding it. Returning both halves in one value
 * keeps the decoding to a single pass and lets the caller decide what to do with the rejections
 * (the repository logs them with their registry context).
 */
final class DecodedConstraints
{
    /**
     * @param list<Constraint> $constraints
     * @param list<array{index: int|string|null, reason: string}> $rejections
     */
    public function __construct(
        private readonly array $constraints = [],
        private readonly array $rejections = [],
    ) {
    }

    /**
     * Null rather than an empty list when nothing usable remains, mirroring the "no validation"
     * default a definition carries when its constraints column is empty.
     *
     * @return list<Constraint>|null
     */
    public function getConstraints(): ?array
    {
        return [] !== $this->constraints ? $this->constraints : null;
    }

    /**
     * @return list<array{index: int|string|null, reason: string}>
     */
    public function getRejections(): array
    {
        return $this->rejections;
    }

    public function hasRejections(): bool
    {
        return [] !== $this->rejections;
    }
}
