<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

/**
 * Outcome of one IMAP synchronisation pass. Mirrors the legacy `syncImap`
 * return shape (`hasError`, `errors`) and exposes typed accessors so the
 * caller doesn't have to deal with the raw array.
 */
final class ImapSyncResult
{
    /**
     * @param list<string> $errors
     */
    public function __construct(
        private readonly array $errors,
    ) {
    }

    public function hasErrors(): bool
    {
        return [] !== $this->errors;
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
