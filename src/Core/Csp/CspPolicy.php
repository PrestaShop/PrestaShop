<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Csp;

use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;

/**
 * A Content Security Policy under construction (directive => source expressions), additive-only so contributors can
 * only widen it. addSource() validates inputs through the rule value objects,
 * so a malformed token never reaches the header.
 */
final class CspPolicy
{
    /**
     * @var array<string, list<string>>
     */
    private array $directives = [];

    /**
     * @return bool false only when the directive or source is malformed;
     * a valid no-op (duplicate, redundant 'none') still returns true
     */
    public function addSource(string $directive, string $source): bool
    {
        $cspDirective = CspDirective::tryFrom($directive);
        if (null === $cspDirective) {
            return false;
        }

        // Coarsen granular script-/style- directives to their parent
        // so a contribution can't emit e.g. script-src-elem without 'self'.
        $directive = $cspDirective->coarsen()->value;

        try {
            $source = (new CspSource($source))->getValue();
        } catch (CspConstraintException) {
            return false;
        }

        if (!isset($this->directives[$directive])) {
            $this->directives[$directive] = [];
        }

        // "'none'" is exclusive: only keep it while the directive is empty, and a real source drops a seeded 'none'.
        if ("'none'" === $source) {
            if ([] === $this->directives[$directive]) {
                $this->directives[$directive][] = $source;
            }

            return true;
        }

        if (["'none'"] === $this->directives[$directive]) {
            $this->directives[$directive] = [];
        }

        if (!in_array($source, $this->directives[$directive], true)) {
            $this->directives[$directive][] = $source;
        }

        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function getDirectives(): array
    {
        return $this->directives;
    }
}
