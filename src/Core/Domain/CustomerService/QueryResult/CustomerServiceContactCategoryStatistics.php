<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

/**
 * Open-thread statistics for a single "customer service" contact category
 * (e.g. "Webmaster", "Customer service").
 */
final class CustomerServiceContactCategoryStatistics
{
    public function __construct(
        private readonly int $contactId,
        private readonly string $name,
        private readonly string $description,
        private readonly int $openThreadsCount,
        private readonly ?int $oldestOpenThreadId,
    ) {
    }

    public function getContactId(): int
    {
        return $this->contactId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOpenThreadsCount(): int
    {
        return $this->openThreadsCount;
    }

    public function getOldestOpenThreadId(): ?int
    {
        return $this->oldestOpenThreadId;
    }
}
