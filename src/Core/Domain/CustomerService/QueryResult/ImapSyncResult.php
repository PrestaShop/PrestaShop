<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

/**
 * Outcome of an IMAP mailbox synchronization.
 */
class ImapSyncResult
{
    /**
     * @var string[]
     */
    private $errors;

    /**
     * @param string[] $errors
     */
    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    public function hasError(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
